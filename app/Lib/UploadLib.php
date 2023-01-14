<?php
namespace App\Lib;


use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadLib
{
    protected static $small = 'small';
    protected static $medium = 'medium';
    protected static $large = 'large';

    protected static function autoResizeWatermark($watermark, $widthFromImage, $heightFromImage)
    {
        $watermark =  Image::make($watermark);
        $configPercent = config('const.upload_lib.watermark_percentage') ?? 10;
        $resizeFromImageWidth = $widthFromImage * ($configPercent / 100);
        $resizeFromImageHeight = $heightFromImage * ($configPercent / 100);
            $width = $watermark->width();
            $height = $watermark->height();
            if ($width > $height) {
                $watermark->resize($resizeFromImageWidth, null, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } else {
                $watermark->resize(null, $resizeFromImageHeight, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
        $watermark->opacity(config('const.upload_lib.watermark_opacity') ?? 50);
        return $watermark;
    }

    /**
     * Auto resize thumbnail base width/height
     *
     * @param \Image $image
     * @param string $disk
     * @param string $pathToFile
     * @param integer $size
     */
    protected static function autoResizeThumb($image, $disk, $pathToFile, $size)
    {
        if (!Storage::disk($disk)->exists($pathToFile)) {
            $width = $image->width();
            $height = $image->height();
            if ($width > $height) {
                $image->resize($size, null, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } else {
                $image->resize(null, $size, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            Storage::disk($disk)->put($pathToFile, $image->stream());
        }
    }

    /**
     * Create thumbnail image
     *
     * @param \Image $image
     * @param string $disk
     * @param string $pathToFile
     * @param boolean $watermark
     */
    public static function thumbnail($image, $disk, $filename, $watermark = false)
    {
        $small = config('const.upload_lib.thumbnail_small') ?? 150;
        $medium = config('const.upload_lib.thumbnail_medium') ?? 420;
        $large = config('const.upload_lib.thumbnail_large') ?? 840;
        if ($watermark) { // under maintanent not ready
            $width = $image->width();
            $height = $image->height();
            if (config('const.upload_lib.watermark_image')) {
                $newWatermark = self::autoResizeWatermark(config('const.upload_lib.watermark_image'), $width, $height);
                $x = config('const.upload_lib.watermark_offset_x') ?? 0;
                $y = config('const.upload_lib.watermark_offset_y') ?? 0;
                $position = config('const.upload_lib.watermark_position') ?? 'top-left';
                $image->insert($newWatermark, $position, $x, $y);
            }
        }
        self::autoResizeThumb($image, $disk, config('const.filePath.large').'/'.$filename, $large);
        self::autoResizeThumb($image, $disk, config('const.filePath.medium').'/'.$filename, $medium);
        self::autoResizeThumb($image, $disk, config('const.filePath.small').'/'.$filename, $small);
    }

    /**
     * Clear thumbnail by default size
     *
     * @param string $disk
     * @param string $pathToFile
     */
    public static function clearSingleThumbnail($disk, $pathToFile)
    {
        if (!empty($pathToFile)) {
            if (Storage::disk($disk)->exists($pathToFile)) {
                Storage::disk($disk)->delete($pathToFile);
            }
            if (Storage::disk($disk.'_'.self::$small)->exists($pathToFile)) {
                Storage::disk($disk.'_'.self::$small)->delete($pathToFile);
            }
            if (Storage::disk($disk.'_'.self::$medium)->exists($pathToFile)) {
                Storage::disk($disk.'_'.self::$medium)->delete($pathToFile);
            }
            if (Storage::disk($disk.'_'.self::$large)->exists($pathToFile)) {
                Storage::disk($disk.'_'.self::$large)->delete($pathToFile);
            }
        }
    }

    /**
     * Clear multiple thumbnail by array
     *
     * @param array $value
     * @param string $disk
     */
    public static function clearMultiThumbnail($values, $disk = 'uploads')
    {
        if (is_array($values) && count((array)$values)) {

            foreach ($values as $pathToFile) {
                self::clearSingleThumbnail($disk, $pathToFile);
            }
        }
    }

    /**
     * Create validate role for base64 image
     */
    public static function registerBase64Validate()
    {
        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            try {
                $image = Image::make($value);
                if (!Str::startsWith($value, 'data:image')) { return true; };
                $size = strlen(base64_decode($value));
                $size_kb = $size / 1024;
                return $size_kb <= $parameters[0];
            } catch (\Exception $e) {
                Log::error('base64image validate: '.$e);
                return false;
            }
        });
        Validator::replacer('base64image', function($message, $attribute, $rule, $parameters) {
            $base64image = $parameters[0];
            return str_replace(':base64image', $base64image, $message);
        });
    }
}
