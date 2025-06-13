<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:convert-webp';
    protected $description = 'Convert all existing images to WebP format and update DB paths';

    public function handle()
    {
        $manager = new ImageManager(new Driver());

        $paths = Image::select('path')
            ->distinct()
            ->where(function ($q) {
                $q->where('path', 'like', '%.jpg')
                    ->orWhere('path', 'like', '%.jpeg')
                    ->orWhere('path', 'like', '%.png');
            })
            ->pluck('path');

        $bar = $this->output->createProgressBar(count($paths));
        $bar->start();

        foreach ($paths as $path) {
            $originalPath = storage_path("app/public/{$path}");

            if (!file_exists($originalPath)) {
                $bar->advance();
                continue;
            }

            $originalSize = filesize($originalPath);
            $targetSize = 300 * 1024;

            // حساب الجودة الديناميكية، مع سقف 90 و أرضية 20
            $scale = $targetSize / $originalSize;
            $quality = min(90, max(10, intval($scale * 100)));

            $originalExtension = pathinfo($path, PATHINFO_EXTENSION);
            $originalDir = pathinfo($path, PATHINFO_DIRNAME);
            $originalName = pathinfo($path, PATHINFO_FILENAME);

            $newName = $originalName . '.webp';
            $newPath = $originalDir . '/' . $newName;
            $fullNewPath = storage_path("app/public/{$newPath}");

            try {
                $img = $manager->read($originalPath)->toWebp(quality: $quality);
                $img->save($fullNewPath);

                // حذف النسخة الأصلية
                unlink($originalPath);

                // تحديث جميع السجلات المرتبطة بهذه الصورة
                Image::where('path', $path)->update(['path' => $newPath]);
            } catch (\Exception $e) {
                $this->error("⚠️ Error in path: {$path} - {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✅ Images converted and paths updated successfully!");
    }
}
