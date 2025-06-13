<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:convert-webp';
    protected $description = 'Convert all existing images to WebP format and update DB paths';

    public function handle()
    {
        $manager = new ImageManager(new Driver());

        $images = Image::where('path', 'like', '%.jpg')
            ->orWhere('path', 'like', '%.jpeg')
            ->orWhere('path', 'like', '%.png')
            ->get();

        $bar = $this->output->createProgressBar(count($images));
        $bar->start();

        foreach ($images as $image) {
            $originalPath = storage_path("app/public/{$image->path}");

            if (!file_exists($originalPath)) {
                $bar->advance();
                continue;
            }

            $newName = pathinfo($image->path, PATHINFO_FILENAME) . '.webp';
            $newPath = pathinfo($image->path, PATHINFO_DIRNAME) . '/' . $newName;
            $fullNewPath = storage_path("app/public/{$newPath}");

            try {
                $img = $manager->read($originalPath)->toWebp(quality: 10);
                $img->save($fullNewPath);

                // حذف النسخة القديمة
                unlink($originalPath);

                // تحديث المسار في قاعدة البيانات
                $image->update(['path' => $newPath]);
            } catch (\Exception $e) {
                $this->error("خطأ في الصورة ID: {$image->id} - {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✅ تم تحويل الصور وتحديث المسارات بنجاح!");
    }
}
