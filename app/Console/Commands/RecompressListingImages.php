<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class RecompressListingImages extends Command
{
    protected $signature = 'images:recompress-listings {--start=1}';
    protected $description = 'Recompress only oversized compressed images using originals';

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
            $targetSize = 100 * 1024;
            $quality = 90;
            $success = false;

            while ($quality >= 10) {
                $tempPath = tempnam(sys_get_temp_dir(), 'webp_');
                $compressed = $image->toWebp(quality: $quality);
                $compressed->save($tempPath);

                $size = filesize($tempPath);

                if ($size <= $targetSize) {
                    File::copy($tempPath, $compressedFilePath);
                    $this->info("[{$position}/{$total}] ✅ {$filename} compressed to " . round($size / 1024) . " KB");
                    $success = true;
                    unlink($tempPath);
                    break;
                }

                unlink($tempPath);
                $quality -= 10;
            }

            if (!$success) {
                $this->warn("[{$position}/{$total}] ❌ Could not compress {$filename} below 100KB");
            }
        }

        $this->info("\n✅ Done from image #{$start}.");
        return 0;
    }
}
