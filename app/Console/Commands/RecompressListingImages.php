<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class RecompressListingImages extends Command
{
    protected $signature = 'images:recompress-listings';
    protected $description = 'Compress listings-main images and override matching ones in listings-compressed if original exists and compressed is > 75KB';

    public function handle()
    {
        $originalsPath = storage_path('app/public/listings-main');
        $compressedPath = storage_path('app/public/listings-compressed');
        $targetSize = 75 * 1024;

        $files = File::files($originalsPath);
        $manager = new ImageManager(new Driver());

        $count = count($files);
        $index = 0;

        foreach ($files as $file) {
            $index++;
            $filename = $file->getFilename();
            $compressedFilePath = $compressedPath . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.webp';

            // Skip if compressed version doesn't exist
            if (!File::exists($compressedFilePath)) {
                $this->info("[{$index}/{$count}] Skipping: {$filename} (no match in listings-compressed)");
                continue;
            }

            // Skip if compressed version is already small enough
            if (filesize($compressedFilePath) <= $targetSize) {
                $this->line("[{$index}/{$count}] Skipping: {$filename} (already <= 75KB)");
                continue;
            }

            try {
                $image = $manager->read($file->getPathname());
                $quality = 90;
                $best = null;
                $bestSize = null;

                while ($quality >= 10) {
                    $temp = tempnam(sys_get_temp_dir(), 'webp_');
                    $compressed = $image->toWebp(quality: $quality);
                    $compressed->save($temp);
                    $size = filesize($temp);
                    unlink($temp);

                    if ($bestSize === null || $size < $bestSize) {
                        $best = $compressed;
                        $bestSize = $size;
                    }

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
            } catch (\Throwable $e) {
                $this->warn("[{$index}/{$count}] ❌ Error processing {$filename}: " . $e->getMessage());
            }
        }

        $this->info("\nAll done. ✅");
        return 0;
    }
}
