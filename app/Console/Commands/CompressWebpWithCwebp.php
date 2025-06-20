<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompressWebpWithCwebp extends Command
{
    protected $signature = 'images:compress-cwebp 
                            {source=storage/app/public/listings : Source folder} 
                            {target=storage/app/public/listings-compressed : Target folder} 
                            {--quality=30 : Compression quality (1-100)}';

    protected $description = 'Compress .webp images using system cwebp and store in new folder';

    public function handle()
    {
        $source = base_path($this->argument('source'));
        $target = base_path($this->argument('target'));
        $quality = (int) $this->option('quality');

        if (!file_exists($source)) {
            $this->error("Source folder not found: $source");
            return;
        }

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $files = glob("{$source}/*.webp");

        if (empty($files)) {
            $this->warn("No .webp files found in: $source");
            return;
        }

        foreach ($files as $file) {
            $filename = basename($file);
            $output = "{$target}/{$filename}";

            $this->line("🔄 Compressing: $filename");

            $cmd = "cwebp -q $quality \"$file\" -o \"$output\"";
            $result = shell_exec($cmd);

            if (file_exists($output)) {
                $this->info("✅ Saved: $output");
            } else {
                $this->error("❌ Failed: $filename");
            }
        }

        $this->info("🎉 All done!");
    }
}
