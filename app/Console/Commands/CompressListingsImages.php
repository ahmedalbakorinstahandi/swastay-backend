<?php

namespace App\Console\Commands;

use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CompressListingsImages extends Command
{
    protected $signature = 'compress:listings {--force : Force re-compression of existing files}';
    protected $description = 'Compress all listing images to 50KB and save in listings-more-compress folder';

    public function handle(): void
    {
        $sourceDirectory = storage_path('app/public/listings');
        $targetDirectory = storage_path('app/public/listings-more-compress');

        // التحقق من وجود المجلد المصدر
        if (!File::exists($sourceDirectory)) {
            $this->error("❌ Directory not found: $sourceDirectory");
            return;
        }

        // إنشاء مجلد الهدف إذا لم يكن موجوداً
        if (!File::exists($targetDirectory)) {
            File::makeDirectory($targetDirectory, 0755, true, true);
            $this->info("📁 Created target directory: listings-more-compress");
        }

        // الحصول على جميع الملفات
        $files = File::allFiles($sourceDirectory);
        $imageFiles = array_filter($files, function ($file) {
            $extension = strtolower($file->getExtension());
            return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
        });

        $total = count($imageFiles);
        
        if ($total === 0) {
            $this->warn("⚠️ No image files found in listings directory");
            return;
        }

        $this->info("🔍 Found $total image files. Starting compression...");
        $this->newLine();

        // إنشاء شريط التقدم
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        foreach ($imageFiles as $file) {
            $fileName = $file->getFilename();
            $targetPath = $targetDirectory . '/' . $fileName;

            try {
                // التحقق من وجود الملف المضغوط مسبقاً
                if (File::exists($targetPath) && !$this->option('force')) {
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }

                // ضغط الصورة إلى 50KB
                $compressedImage = ImageService::compressImage(
                    $file->getRealPath(),
                    75 * 1024, // 50KB
                    10,        // minQuality
                    90,        // maxQuality
                    true       // forceTargetSize
                );

                // حفظ الصورة المضغوطة
                $compressedImage->save($targetPath);

                $successCount++;
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("❌ Failed to compress: $fileName - " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // عرض النتائج
        $this->info("✅ Compression completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Success', $successCount],
                ['⚠️ Skipped', $skippedCount],
                ['❌ Errors', $errorCount],
                ['📊 Total', $total],
            ]
        );

        if ($errorCount > 0) {
            $this->warn("⚠️ Some files failed to compress. Check the errors above.");
        }

        $this->info("📁 Compressed images saved in: listings-more-compress/");
    }
} 