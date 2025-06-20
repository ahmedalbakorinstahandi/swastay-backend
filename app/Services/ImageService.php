<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\File;
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

        // ضغط الصورة إلى الحجم المطلوب
        $compressedImage = self::compressImage($image, 300 * 1024, 10, 90, false);

        // حفظ الصورة المضغوطة
        $compressedImage->save($new_path);

        if ($copyFolderMoreCompress) {
            $compressedImageMoreCompress = self::compressImage($image, 75 * 1024, 10, 90, true);
            self::MakeFolder("{$folder}-more-compress");

            $compressedImageMoreCompress->save(storage_path("app/public/{$folder}-more-compress/{$imageName}"));
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

        // قراءة الصورة
        $imageContent = $manager->read($image);

        // الحصول على حجم الصورة الأصلية
        $originalSize = is_string($image) && file_exists($image) ? filesize($image) : (is_object($image) && method_exists($image, 'getSize') ? $image->getSize() : null);

        // إذا كان الحجم الأصلي أصغر من الحجم المستهدف، لا حاجة للضغط
        if ($originalSize && $originalSize <= $targetSize) {
            return $imageContent->toWebp(quality: $maxQuality);
        }

        // بدء من أعلى جودة وتقليلها تدريجياً حتى نصل للحجم المطلوب
        $quality = $maxQuality;
        $bestCompressedImage = null;
        $bestSize = null;

        do {
            $compressedImage = $imageContent->toWebp(quality: $quality);

            // حفظ مؤقت لقياس الحجم
            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            $compressedImage->save($tempFile);
            $newSize = filesize($tempFile);
            unlink($tempFile);

            // حفظ أفضل نتيجة حتى الآن
            if ($bestSize === null || $newSize < $bestSize) {
                $bestSize = $newSize;
                $bestCompressedImage = $compressedImage;
            }

            // إذا وصلنا للحجم المطلوب، نخرج من الحلقة
            if ($newSize <= $targetSize) {
                break;
            }

            // تقليل الجودة بمقدار 10
            $quality -= 10;
        } while ($quality >= $minQuality);

        // إذا لم نتمكن من الوصول للحجم المطلوب
        if ($quality < $minQuality && $forceTargetSize) {
            // الاستمرار في الضغط حتى نصل للحجم المطلوب أو نصل لأقل جودة ممكنة (1)
            while ($quality >= 1) {
                $compressedImage = $imageContent->toWebp(quality: $quality);

                $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                $compressedImage->save($tempFile);
                $newSize = filesize($tempFile);
                unlink($tempFile);

                if ($newSize <= $targetSize) {
                    return $compressedImage;
                }

                $quality -= 5; // تقليل أبطأ في النهاية
            }

            // إذا لم نتمكن من الوصول للحجم المطلوب حتى مع أقل جودة، نعيد أفضل نتيجة
            return $bestCompressedImage;
        }

        // إرجاع أفضل نتيجة تم التوصل إليها ضمن نطاق الجودة المحدد
        return $bestCompressedImage;
    }
}
