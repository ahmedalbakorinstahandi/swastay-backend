<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class RecompressListingImages extends Command
{
    protected $signature = 'images:recompress-listings';
    protected $description = 'Compress listings-main images and override matching ones in listings-compressed if original exists';

    public function handle()
    {
        $originalsPath = storage_path('app/public/listings-main');
        $compressedPath = storage_path('app/public/listings-compressed');

        $files = File::files($originalsPath);
        $manager = new ImageManager(new Driver());

        $count = count($files);
        $index = 0;

        foreach ($files as $file) {
            $index++;
            $filename = $file->getFilename();
            $baseName = pathinfo($filename, PATHINFO_FILENAME);

            // ابحث عن أول ملف يطابق الاسم الأساسي في compressed
            $match = collect(File::files($compressedPath))
                ->first(fn($f) => pathinfo($f->getFilename(), PATHINFO_FILENAME) === $baseName);

            if (!$match) {
                $this->info("[{$index}/{$count}] Skipping: {$filename} (no match in listings-compressed)");
                continue;
            }

            $compressedFilePath = $match->getPathname();

            try {
                $image = $manager->read($file->getPathname());
            } catch (\Throwable $e) {
                $this->warn("[{$index}/{$count}] ❌ Failed to read image: {$filename}");
                continue;
            }

            $quality = 90;
            $targetSize = 75 * 1024;
            $best = null;
            $bestSize = null;

            while ($quality >= 10) {
                $temp = tempnam(sys_get_temp_dir(), 'webp_');
                try {
                    $compressed = $image->toWebp(quality: $quality);
                    $compressed->save($temp);
                    $size = filesize($temp);
                } catch (\Throwable $e) {
                    unlink($temp);
                    break;
                }

                if ($bestSize === null || $size < $bestSize) {
                    $best = $compressed;
                    $bestSize = $size;
                }

                unlink($temp);

                if ($size <= $targetSize) {
                    break;
                }

                $quality -= 10;
            }

            if ($best) {
                $best->save($compressedFilePath);
                $this->info("[{$index}/{$count}] ✅ {$filename} compressed to " . round($bestSize / 1024) . " KB");
            } else {
                $this->warn("[{$index}/{$count}] ❌ Failed to compress {$filename}");
            }
        }

        $this->info("\nAll done. ✅");
        return 0;
    }
}
