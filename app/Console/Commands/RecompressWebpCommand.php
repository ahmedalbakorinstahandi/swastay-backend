<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class RecompressWebpCommand extends Command
{
    protected $signature = 'images:recompress-webp {sourceFolder} {--targetFolder=compressed} {--size=51200}';
    protected $description = 'Aggressively recompress all .webp images from source folder into target folder';

    public function handle()
    {
        $source = $this->argument('sourceFolder');
        $target = $this->option('targetFolder');
        $targetSize = (int) $this->option('size');

        $manager = new ImageManager(new Driver());

        $sourcePath = storage_path("app/public/{$source}");
        $targetPath = storage_path("app/public/{$target}");

        if (!is_dir($sourcePath)) {
            $this->error("Source folder does not exist: {$sourcePath}");
            return;
        }

        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        $files = glob("{$sourcePath}/*.webp");

        if (empty($files)) {
            $this->warn("No .webp files found in: {$sourcePath}");
            return;
        }

        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            $this->info("🔄 Processing: $fileName");

            try {
                $originalSize = filesize($filePath);
                if ($originalSize <= $targetSize) {
                    $this->line("✅ Already small enough ({$originalSize} bytes), skipping.");
                    copy($filePath, "{$targetPath}/{$fileName}");
                    continue;
                }

                $image = $manager->read($filePath);
                $scales = [0.5, 0.3, 0.1, 0.05];
                $compressedPath = null;

                foreach ($scales as $scale) {
                    $resized = $image->scale($scale);
                    $compressed = $resized->toWebp(quality: 1);

                    $temp = tempnam(sys_get_temp_dir(), 'webp_');
                    $compressed->save($temp);
                    $size = filesize($temp);

                    if ($size <= $targetSize) {
                        copy($temp, "{$targetPath}/{$fileName}");
                        unlink($temp);
                        $this->line("✅ Compressed to {$size} bytes → Saved to {$target}/{$fileName}");
                        $compressedPath = true;
                        break;
                    }

                    unlink($temp);
                }

                if (!$compressedPath) {
                    $this->warn("⚠️ Could not compress {$fileName} under {$targetSize} bytes. Copying as-is.");
                    copy($filePath, "{$targetPath}/{$fileName}");
                }

            } catch (\Throwable $e) {
                $this->error("❌ Failed to process {$fileName}: {$e->getMessage()}");
                continue;
            }
        }

        $this->info("🎉 Recompression complete.");
    }
}
