<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompressWebpWithCwebp extends Command
{
    protected $signature = 'images:compress-cwebp 
                            {source=storage/app/public/listings : Source folder} 
                            {target=storage/app/public/listings-compressed : Target folder} 
                            {--quality=30 : Compression quality (1-100)}';

    protected $description = 'Compress .webp images using cwebp and avoid going under 75KB';

    public function handle()
    {
        $source = base_path($this->argument('source'));
        $target = base_path($this->argument('target'));
        $quality = (int) $this->option('quality');
        $minSize = 75 * 1024; // 75KB

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

            $this->line("🔄 [$progress%] Processing: $filename");

            $tempFile = tempnam(sys_get_temp_dir(), 'webp_');
            $cmd = "cwebp -q $quality \"$file\" -o \"$tempFile\"";
            shell_exec($cmd);

            if (!file_exists($tempFile)) {
                $this->error("❌ Failed to compress: $filename");
                continue;
            }

            $compressedSize = filesize($tempFile);

            if ($compressedSize >= $minSize) {
                copy($tempFile, $output);
                $this->info("✅ [$progress%] Compressed: $filename (" . round($compressedSize / 1024) . " KB)");
            } else {
                copy($file, $output);
                $this->warn("⚠️ [$progress%] Skipped compression (too small): $filename (" . round($compressedSize / 1024) . " KB)");
            }

            unlink($tempFile);
        }

        $this->info("🎉 Done compressing all images.");
    }
}
