<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class OptimizeListingsImages extends Command
{
    protected $signature = 'optimize:listings';
    protected $description = 'Compress all images in storage/app/public/listings_copy';

    public function handle(): void
    {
        $directory = storage_path('app/public/listings_copy');

        if (!File::exists($directory)) {
            $this->error("❌ Directory not found: $directory");
            return;
        }

        $files = File::allFiles($directory);
        $total = count($files);
        $this->info("🔍 Found $total files. Starting optimization...");

        $i = 1;
        foreach ($files as $file) {
            $this->info("[$i/$total] Compressing: " . $file->getRelativePathname());

            try {
                ImageOptimizer::optimize($file->getRealPath());
            } catch (\Exception $e) {
                $this->error("⚠️ Failed: " . $e->getMessage());
            }

            $i++;
        }

        $this->info("✅ Done compressing all images.");
    }
}
