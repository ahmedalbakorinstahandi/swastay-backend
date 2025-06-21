<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class RecompressListingImages extends Command
{
    protected $signature = 'images:recompress-listings {--start=1}';
    protected $description = 'Recompress listings-main images only if existing compressed version is > 100KB';

    public function handle()
    {
        $originalsPath = storage_path('app/public/listings-main');
        $compressedPath = storage_path('app/public/listings-compressed');

        $files = File::files($originalsPath);
        $manager = new ImageManager(new Driver());

        $total = count($files);
        $start = (int) $this->option('start');

        foreach ($files as $index => $file) {
            $position = $index + 1;
            if ($position < $start) continue;

            $filename = $file->getFilename();
            $compressedFilePath = $compressedPath . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.webp';

            if (!File::exists($compressedFilePath)) {
                $this->line("[{$position}/{$total}] Skipping: {$filename} (no match in listings-compressed)");
                continue;
            }

            $currentSize = filesize($compressedFilePath);
            if ($currentSize <= 100 * 1024) {
                $this->line("[{$position}/{$total}] Skipping: {$filename} (already <= 100KB)");
                continue;
            }

            $image = $manager->read($file->getPathname());
            $quality = 90;
            $targetSize = 100 * 1024;
            $best = null;
            $bestSize = null;

            while ($quality >= 10) {
                $temp = tempnam(sys_get_temp_dir(), 'webp_');
                $compressed = $image->toWebp(quality: $quality);
                $compressed->save($temp);
                $size = filesize($temp);

                if ($bestSize === null || $size < $bestSize) {
                    $best = $compressed;
                    $bestSize = $size;
                }

                if ($size <= $targetSize) {
                    unlink($temp);
                    break;
                }

                unlink($temp);
                $quality -= 10;
            }

            if ($best) {
                $best->save($compressedFilePath);
                $this->info("[{$position}/{$total}] ✅ {$filename} compressed to " . round($bestSize / 1024) . " KB");
            } else {
                $this->warn("[{$position}/{$total}] ❌ Failed to compress {$filename}");
            }
        }

        $this->info("\n✅ Done from image #{$start}.");
        return 0;
    }
}
