<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CompressSpecificImages extends Command
{
    protected $signature = 'compress:images {--ids=* : Specific image IDs to compress} {--force : Force re-compression}';
    protected $description = 'Compress specific images to 50KB and save in listings-more-compress folder';

    public function handle(): void
    {
        $targetDirectory = storage_path('app/public/listings-more-compress');

        // إنشاء مجلد الهدف إذا لم يكن موجوداً
        if (!File::exists($targetDirectory)) {
            File::makeDirectory($targetDirectory, 0755, true, true);
            $this->info("📁 Created target directory: listings-more-compress");
        }

        // الحصول على الصور من قاعدة البيانات
        $query = Image::where('path', 'like', 'listings/%');
        
        if ($this->option('ids')) {
            $query->whereIn('id', $this->option('ids'));
        }

        $images = $query->get();

        if ($images->isEmpty()) {
            $this->warn("⚠️ No images found matching the criteria");
            return;
        }

        $total = $images->count();
        $this->info("🔍 Found $total images. Starting compression...");
        $this->newLine();

        // إنشاء شريط التقدم
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        foreach ($images as $image) {
            $sourcePath = storage_path('app/public/' . $image->path);
            $fileName = basename($image->path);
            $targetPath = $targetDirectory . '/' . $fileName;

            try {
                // التحقق من وجود الملف المصدر
                if (!File::exists($sourcePath)) {
                    $this->newLine();
                    $this->warn("⚠️ Source file not found: $fileName");
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }

                // التحقق من وجود الملف المضغوط مسبقاً
                if (File::exists($targetPath) && !$this->option('force')) {
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }

                // ضغط الصورة إلى 50KB
                $compressedImage = ImageService::compressImage(
                    $sourcePath,
                    50 * 1024, // 50KB
                    1,         // minQuality - أقل جودة ممكنة
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