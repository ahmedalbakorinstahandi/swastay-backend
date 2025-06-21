<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    private static function MakeFolder($folderName)
    {
        $pathFolder = storage_path(sprintf('app/public/%s', $folderName));

        if (!File::isDirectory($pathFolder)) {
            File::makeDirectory($pathFolder, 0755, true, true);
        }
    }



    public static function storeImage($image, $folder, $copyFolderMoreCompress = false)
    {
        self::MakeFolder($folder);

        $baseName =  uniqid();
        $imageName = $baseName . '.webp';
        $new_path = storage_path("app/public/{$folder}/{$imageName}");
        $main_path = storage_path("app/public/{$folder}-main/{$imageName}");

        $path = $image->storeAs($folder . '-main', $baseName . '.' . $image->getClientOriginalExtension(), 'public');

        Log::info($path);

        // ضغط الصورة إلى الحجم المطلوب
        $compressedImage = self::compressImage($image, 300 * 1024, 10, 90, false);

        // حفظ الصورة المضغوطة
        $compressedImage->save($new_path);

        if ($copyFolderMoreCompress) {
            $compressedImageMoreCompress = self::compressImage($image, 50 * 1024, 1, 90, true);
            self::MakeFolder("{$folder}-compressed");

            $compressedImageMoreCompress->save(storage_path("app/public/{$folder}-compressed/{$imageName}"));
        }

        return "{$folder}/{$imageName}";
    }

    public static function updateImage($image, $folder, $oldImageName): string|null
    {
        // return ImageService::deleteImage($oldImageName) ? ImageService::storeImage($image, $folder) : null;
        return ImageService::storeImage($image, $folder);
    }

    public static function deleteImage($imagePath)
    {
        if (strpos($imagePath, asset("storage/")) !== false) {
            $imagePath = str_replace(asset("storage/"), "", $imagePath);
        }

        if (Storage::disk('public')->exists($imagePath)) {
            return  Storage::disk('public')->delete($imagePath);
        }

        return false;
    }

    public static function removeImages($ids)
    {
        if (!is_array($ids)) {
            abort(
                response()->json([
                    'success' => false,
                    'message' =>  'يجب تقديم معرفات الصور كمصفوفة غير فارغة.',
                ], 422),
            );
        }

        $existingImages = Image::whereIn('id', $ids)->get();

        if (count($ids) !== $existingImages->count()) {
            abort(
                response()->json([
                    'success' => false,
                    'message' => 'بعض الصور المطلوب حذفها غير موجودة.',
                ], 422),
            );
        }

        foreach ($existingImages as $image) {
            if (Storage::exists("public/" . $image->path)) {
                Storage::delete("public/" . $image->path);
            }
            $image->delete();
        }
    }

    /**
     * ضغط الصورة إلى الحجم المطلوب
     * @param mixed $image الصورة المراد ضغطها
     * @param int $targetSize الحجم المستهدف بالبايت
     * @param int $minQuality أقل جودة مسموحة (افتراضي: 10)
     * @param int $maxQuality أعلى جودة مسموحة (افتراضي: 90)
     * @param bool $forceTargetSize إذا كان true، سيستمر في الضغط حتى يصل للحجم المطلوب حتى لو وصل للحد الأدنى
     * @return \Intervention\Image\Interfaces\ImageInterface الصورة المضغوطة
     */
    public static function compressImage($image, $targetSize, $minQuality = 10, $maxQuality = 90, $forceTargetSize = false)
    {
        $manager = new ImageManager(new Driver());
        $imageContent = $manager->read($image);

        // تحديد إذا الصورة WebP
        $extension = is_string($image) ? strtolower(pathinfo($image, PATHINFO_EXTENSION)) : null;
        $isWebP = $extension === 'webp';

        // الحصول على حجم الصورة الأصلية
        $originalSize = is_string($image) && file_exists($image) ? filesize($image) : null;

        // إذا كانت أصغر من المطلوب، نعيدها كما هي
        if ($originalSize && $originalSize <= $targetSize) {
            return $imageContent->toWebp(quality: $maxQuality);
        }

        if ($isWebP) {
            // مسار WebP فقط
            $scales = [0.3, 0.2, 0.1, 0.05];

            foreach ($scales as $scale) {
                try {
                    $resized = $imageContent->scale($scale);
                    $compressed = $resized->toWebp(quality: 1);

                    $temp = tempnam(sys_get_temp_dir(), 'img_');
                    $compressed->save($temp);
                    $size = filesize($temp);
                    unlink($temp);

                    if ($size <= $targetSize || $forceTargetSize) {
                        return $compressed;
                    }
                } catch (\Throwable) {
                    continue;
                }
            }

            return $imageContent->toWebp(quality: 1);
        }

        // باقي الصيغ (JPEG, PNG...)
        $quality = $maxQuality;
        $bestImage = null;
        $bestSize = null;

        do {
            $compressed = $imageContent->toWebp(quality: $quality);
            $temp = tempnam(sys_get_temp_dir(), 'img_');
            $compressed->save($temp);
            $size = filesize($temp);
            unlink($temp);

            if ($bestSize === null || $size < $bestSize) {
                $bestSize = $size;
                $bestImage = $compressed;
            }

            if ($size <= $targetSize) {
                break;
            }

            $quality -= 10;
        } while ($quality >= $minQuality);

        return $bestImage ?: $imageContent->toWebp(quality: $minQuality);
    }

    // use Intervention\Image\Drivers\Gd\Driver;
    // use Intervention\Image\ImageManager;

    function aggressivelyRecompressWebpFolder($folderPath, $targetSize = 50 * 1024)
    {
        $manager = new ImageManager(new Driver());
        $fullPath = storage_path("app/public/{$folderPath}");
        $files = glob("{$fullPath}/*.webp");

        foreach ($files as $filePath) {
            echo "Processing: $filePath\n";

            try {
                $originalSize = filesize($filePath);
                if ($originalSize <= $targetSize) {
                    echo "- Already small enough ({$originalSize} bytes), skipping.\n";
                    continue;
                }

                $image = $manager->read($filePath);

                $scales = [0.5, 0.3, 0.1, 0.05];
                foreach ($scales as $scale) {
                    $resized = $image->scale($scale);
                    $compressed = $resized->toWebp(quality: 1);

                    $temp = tempnam(sys_get_temp_dir(), 'webp_');
                    $compressed->save($temp);
                    $size = filesize($temp);

                    if ($size <= $targetSize) {
                        copy($temp, $filePath);
                        unlink($temp);
                        echo "- Compressed and replaced: {$size} bytes\n";
                        break;
                    }

                    unlink($temp);
                }
            } catch (\Throwable $e) {
                echo "- Failed to process: $filePath\n";
                continue;
            }
        }
    }
}
