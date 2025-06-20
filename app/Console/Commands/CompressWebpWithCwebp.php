<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompressWebpWithCwebp extends Command
{
    protected $signature = 'images:compress-cwebp 
                            {source=storage/app/public/listings : Source folder} 
                            {target=storage/app/public/listings-compressed : Target folder}';

    protected $description = 'Compress each WebP image to get ~75KB based on its current size';

    public function handle()
    {
        $source = base_path($this->argument('source'));
        $target = base_path($this->argument('target'));
        $targetSize = 75 * 1024; // 75 KB

        if (!file_exists($source)) {
            $this->error("❌ Source folder not found: $source");
            return;
        }

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $files = glob("{$source}/*.webp");
        $total = count($files);

        if ($total === 0) {
            $this->warn("⚠️ No .webp files found in: $source");
            return;
        }

        foreach ($files as $index => $file) {
            $filename = basename($file);
            $output = "{$target}/{$filename}";
            $progress = round((($index + 1) / $total) * 100);

            $originalSize = filesize($file);

            // 1. احسب النسبة بين المطلوب والحجم الحالي
            $ratio = $targetSize / $originalSize;

            // 2. حوّلها إلى جودة تقريبية بين 10 و 100
            $estimatedQuality = (int) max(10, min(100, round($ratio * 100)));

            // 3. اضغط بجودة محسوبة
            $tempFile = tempnam(sys_get_temp_dir(), 'webp_');
            $cmd = "cwebp -q $estimatedQuality \"$file\" -o \"$tempFile\"";
            shell_exec($cmd);

            if (!file_exists($tempFile)) {
                $this->error("❌ [$progress%] Failed: $filename");
                continue;
            }

            $finalSize = filesize($tempFile);

            copy($tempFile, $output);
            unlink($tempFile);

            $this->info("✅ [$progress%] $filename | Orig: ".round($originalSize/1024)." KB → Final: ".round($finalSize/1024)." KB | Q=$estimatedQuality");
        }

        $this->info("🎯 Done compressing all images to ~75KB.");
    }
}
