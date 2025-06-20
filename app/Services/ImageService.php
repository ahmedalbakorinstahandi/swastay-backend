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
            // ضغط الصورة الأصلية مباشرة إلى 50KB (وليس الصورة المضغوطة)
            $compressedImageMoreCompress = self::compressImage($image, 50 * 1024, 1, 90, true);
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

        // إذا كان forceTargetSize = true، نستخدم ضغط قوي جداً
        if ($forceTargetSize) {
            // محاولة 1: ضغط بجودة منخفضة جداً
            $compressedImage = $imageContent->toWebp(quality: 1);
            
            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            $compressedImage->save($tempFile);
            $newSize = filesize($tempFile);
            unlink($tempFile);

            if ($newSize <= $targetSize) {
                return $compressedImage;
            }

            // محاولة 2: تقليل الحجم بنسبة 50% + ضغط قوي
            try {
                $resizedImage = $imageContent->scale(0.5);
                $compressedImage = $resizedImage->toWebp(quality: 1);
                
                $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                $compressedImage->save($tempFile);
                $newSize = filesize($tempFile);
                unlink($tempFile);

                if ($newSize <= $targetSize) {
                    return $compressedImage;
                }
            } catch (\Exception $e) {
                // تجاهل الأخطاء
            }

            // محاولة 3: تقليل الحجم بنسبة 25% + ضغط قوي
            try {
                $resizedImage = $imageContent->scale(0.25);
                $compressedImage = $resizedImage->toWebp(quality: 1);
                
                $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                $compressedImage->save($tempFile);
                $newSize = filesize($tempFile);
                unlink($tempFile);

                if ($newSize <= $targetSize) {
                    return $compressedImage;
                }
            } catch (\Exception $e) {
                // تجاهل الأخطاء
            }

            // محاولة 4: تقليل الحجم بنسبة 10% + ضغط قوي
            try {
                $resizedImage = $imageContent->scale(0.1);
                $compressedImage = $resizedImage->toWebp(quality: 1);
                
                $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                $compressedImage->save($tempFile);
                $newSize = filesize($tempFile);
                unlink($tempFile);

                return $compressedImage; // نعيد النتيجة حتى لو لم تصل للحجم المطلوب
            } catch (\Exception $e) {
                // إذا فشل كل شيء، نعيد الصورة الأصلية مضغوطة بجودة منخفضة
                return $imageContent->toWebp(quality: 1);
            }
        }

        // إذا لم يكن forceTargetSize = true، نستخدم الضغط العادي
        $quality = $maxQuality;
        $bestCompressedImage = null;
        $bestSize = null;

        do {
            $compressedImage = $imageContent->toWebp(quality: $quality);

            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            $compressedImage->save($tempFile);
            $newSize = filesize($tempFile);
            unlink($tempFile);

            if ($bestSize === null || $newSize < $bestSize) {
                $bestSize = $newSize;
                $bestCompressedImage = $compressedImage;
            }

            if ($newSize <= $targetSize) {
                break;
            }

            $quality -= 10;
        } while ($quality >= $minQuality);

        return $bestCompressedImage ?: $imageContent->toWebp(quality: $minQuality);
    }
}
