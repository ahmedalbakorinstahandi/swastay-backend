<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixHeavyWebpCommand extends Command
{
    protected $signature = 'images:fix-heavy-webp 
                            {folder=storage/app/public/listings-compressed : Folder to scan} 
                            {--max=76800 : Max size in bytes (default 75KB)}';

    protected $description = 'Recompress .webp images over size limit using cwebp and fallback resize';

    public function handle()
    {
        $folder = base_path($this->argument('folder'));
        $maxSize = (int) $this->option('max');

        if (!is_dir($folder)) {
            $this->error("❌ Folder not found: $folder");
            return;
        }

        $files = glob("$folder/*.webp");
        $total = count($files);
        if (!$total) {
            $this->warn("⚠️ No .webp files found.");
            return;
        }

        foreach ($files as $i => $file) {
            $filename = basename($file);
            $progress = round(($i + 1) / $total * 100);
            $size = filesize($file);

            if ($size <= $maxSize) {
                $this->line("✅ [$progress%] OK: $filename (".round($size / 1024)." KB)");
                continue;
            }

            $this->warn("🔧 [$progress%] Too big: $filename (".round($size / 1024)." KB)");

            // المحاولة 1: إعادة ضغط فقط
            $temp1 = tempnam(sys_get_temp_dir(), 'webp_');
            shell_exec("cwebp -q 10 \"$file\" -o \"$temp1\"");
            if (file_exists($temp1) && filesize($temp1) <= $maxSize) {
                copy($temp1, $file);
                unlink($temp1);
                $this->info("✔️  Fixed with re-compress only: $filename");
                continue;
            }

            // المحاولة 2: تصغير الأبعاد + ضغط
            $temp2 = tempnam(sys_get_temp_dir(), 'webp_');
            shell_exec("cwebp -resize 1920 1080 -q 30 \"$file\" -o \"$temp2\"");
            if (file_exists($temp2) && filesize($temp2) <= $maxSize) {
                copy($temp2, $file);
                unlink($temp2);
                $this->info("🪄  Fixed with resize: $filename");
                continue;
            }

            // فشل
            if (file_exists($temp1)) unlink($temp1);
            if (file_exists($temp2)) unlink($temp2);
            $this->error("❌ [$progress%] Failed to reduce: $filename (still ".round($size / 1024)." KB)");
        }

        $this->info("🎯 Fixing complete.");
    }
}
