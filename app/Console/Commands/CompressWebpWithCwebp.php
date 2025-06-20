<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompressWebpWithCwebp extends Command
{
    protected $signature = 'images:compress-cwebp {source=storage/app/public/listings : Source folder} {target=storage/app/public/listings-compressed : Target folder}';

    protected $description = 'Compress WebP images to ~75KB dynamically using quality adjustment';

    public function handle()
    {
        $source = base_path($this->argument('source'));
        $target = base_path($this->argument('target'));
        $targetSize = 75 * 1024; // 75KB
        $tolerance = 5 * 1024;   // ±5KB

        if (!file_exists($source)) {
            $this->error("❌ Source folder not found: $source");
            return;
        }

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $files = glob("{$source}/*.webp");
        $total = count($files);

        if (empty($files)) {
            $this->warn("⚠️ No .webp files found in: $source");
            return;
        }

        foreach ($files as $index => $file) {
            $filename = basename($file);
            $output = "{$target}/{$filename}";
            $progress = round((($index + 1) / $total) * 100);

            $this->line("🔄 [$progress%] Processing: $filename");

            $low = 10;
            $high = 100;
            $bestMatch = null;
            $bestSizeDiff = PHP_INT_MAX;

            while ($low <= $high) {
                $mid = (int)(($low + $high) / 2);
                $tempFile = tempnam(sys_get_temp_dir(), 'webp_');
                shell_exec("cwebp -q $mid \"$file\" -o \"$tempFile\"");

                if (!file_exists($tempFile)) {
                    $this->error("❌ Failed to generate: $filename at q=$mid");
                    break;
                }

                $size = filesize($tempFile);
                $diff = abs($targetSize - $size);

                // حفظ أفضل محاولة
                if ($diff < $bestSizeDiff) {
                    $bestSizeDiff = $diff;
                    $bestMatch = [
                        'file' => $tempFile,
                        'quality' => $mid,
                        'size' => $size
                    ];
                } else {
                    unlink($tempFile);
                }

                if ($size > $targetSize + $tolerance) {
                    $low = $low;  // نحتاج تقليل الجودة أكثر
                    $high = $mid - 1;
                } elseif ($size < $targetSize - $tolerance) {
                    $low = $mid + 1;  // نحتاج رفع الجودة شوي
                } else {
                    // ضمن النطاق المطلوب
                    break;
                }
            }

            if ($bestMatch) {
                copy($bestMatch['file'], $output);
                unlink($bestMatch['file']);
                $this->info("✅ [$progress%] Saved: $filename @ q={$bestMatch['quality']} (" . round($bestMatch['size'] / 1024) . " KB)");
            } else {
                $this->warn("⚠️ [$progress%] Skipped: $filename (compression failed)");
            }
        }

        $this->info("🎯 Finished compressing all images to ~75KB");
    }
}
