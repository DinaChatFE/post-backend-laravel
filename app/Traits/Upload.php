<?php
namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Lib\UploadLib;

trait Upload
{
    /*
    |--------------------------------------------------------------------------
    | BACKEND SINGLE UPLOAD
    | @param file $file
    | @return string
    |--------------------------------------------------------------------------
    */
    public function singleUpload($attr, $request) // Taking input image as parameter
    {
        $filename = '';
        if($request->hasFile($attr)){
            $file = $request->file($attr);
            if ($file->isValid()) {
                $filename = self::generatFileName($file);
                // CREATE THUMBNAIL
                self::createThumbnail($file, $filename);
                // ORIGINAL
                $file->storeAs(config('const.filePath.original'), $filename, 'uploads');
                // $file->storeAs(config('const.filePath.original'), $filename, 'spaces');
            }
        }
        return $filename;
    }

    /*
    |--------------------------------------------------------------------------
    | BACKEND MULTIPLE UPLOADS
    | @param attribute name $attr
    | @param request $request
    | @return array
    |--------------------------------------------------------------------------
    */
    public function multipleUploads($attr, $request) // Taking input image as parameter
    {
        $returnImages = [];
        if($request->hasFile($attr) && is_array($request->file($attr))){
            $files = $request->file($attr);
            foreach($files as $file){
                if ($file->isValid()) {
                    $filename = self::generatFileName($file);
                    // CREATE THUMBNAIL
                    self::createThumbnail($file, $filename);
                    // ORIGINAL
                    $file->storeAs(config('const.filePath.original'), $filename, 'uploads');
                    $returnImages[] = $filename;
                }
            }
        }
        return json_encode($returnImages);
    }

    /*
    |--------------------------------------------------------------------------
    | FOR API BASE64 UPLOAD
    | @return string
    |--------------------------------------------------------------------------
    */
    public function base64Upload($value, $thumbnail = true, $mainImage = true)
    {
        $filename = '';
        $disk = 'public';
        if (Str::startsWith($value, 'data:')) {

            $extension = self::checkBase64Extension($value);

            $image = Image::make($value);
            $filename = md5($value . time()) . $extension;
            if ($mainImage) {
                $fileNameMain = config('const.filePath.original') . '/' . $filename;
                Storage::disk($disk)->put($fileNameMain, $image->stream());
            }
            if ($thumbnail) {
                UploadLib::thumbnail($image, $disk, $filename);
            }
        }
        return $fileNameMain ?? '';
    }

    /**
     * Upload with multiple array, provide $value attribute as array
     * Loop insert to storage paths
     * @param array $values
     * @param boolean $thumbnail
     * @param boolean $mainImage
     */
    public function base64Uploads($values, $thumbnail = true, $mainImage = true)
    {
        $fileNameMainArray = [];
        $disk = 'public';
        if(!empty($values) && is_array($values)){
            foreach($values as $key => $value){
                // checking if it's base64 format
                if (Str::startsWith($value, 'data:image')) {
                    $extension = self::checkBase64Extension($value);
                    $image = Image::make($value);
                    $filename = md5($value . time()) . $extension;
                    if ($mainImage) {
                        $fileNameMain = config('const.filePath.original')  . '/'. $filename;
                        Storage::disk($disk)->put($fileNameMain, $image->stream());
                        $fileNameMainArray[] = $fileNameMain;
                    }
                    if ($thumbnail) {
                        UploadLib::thumbnail($image, $disk, $filename);
                    }
                }
            }
        }
        return json_encode($fileNameMainArray);
    }

    /*
    |--------------------------------------------------------------------------
    | GET IMAGE UPLOAD
    | @return string
    |--------------------------------------------------------------------------
    */
    public function getUploadImage($image, $size = 'medium')
    {
        $returnImage = '';
        // RETURN DEFUALT IMAGE
        if(empty($image)):
            $returnImage = config('const.filePath.default');
        else:
            $extension = self::getStringAfterLastDot($image);
            if(self::checkImageExtension($extension)):
                $returnImage = self::switchImageSize($image, $size);
            else:
                $returnImage = self::switchImageSize($image, 'original');
            endif;

        endif;

        return $returnImage;
    }

    /**
     * Get File insert path by folder separate
     *
     * @param string $images
     * @param string $size
     * @return string
     */
    public function getFilePath($images)
    {
        return url('/public' . $images);
    }

    static function switchImageSize($images, $size = 'medium')
    {
        switch($size){
            case 'small':
                return  config('const.filePath.small').$images;
            break;

            case 'medium':
                return  config('const.filePath.medium').$images;
            break;

            case 'large':
                return  config('const.filePath.large').$images;
            break;

            default :
                return  config('const.filePath.original').$images;
            break;
        }
    }

    static function createThumbnail($file, $filename)
    {
        if(self::checkImageExtension($file->getClientOriginalExtension())):
            self::uploadThumbnail($file, config('const.filePath.small').$filename, 150, 93);
            self::uploadThumbnail($file, config('const.filePath.medium').$filename, 300, 185);
            self::uploadThumbnail($file, config('const.filePath.large').$filename, 550, 340);
        endif;
    }

    static function uploadThumbnail($file, $path, $width, $heigh)
    {

        Image::make($file->getRealPath())->resize($width, $heigh,
            function ($constraint) {
                $constraint->aspectRatio();
            })
        ->save(public_path().'/'.$path);
    }

    static function generatFileName($file)
    {
        if(!empty($file)):
            return md5($file->getClientOriginalName() . random_int(1, 9999) . time()) . '.' . $file->getClientOriginalExtension();
        endif;
        return null;
    }

    static function getStringAfterLastDot($string)
    {
        if(!empty($string)):
            return substr(strrchr($string, '.'), 1);
        endif;
        return null;
    }

    static function checkImageExtension($extension)
    {
        if(!empty($extension) && in_array($extension, ['jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'ico', 'cur'])):
            return true;
        endif;
        return false;
    }

    /**
     * checkExtension
     *
     * @param string $value
     * @return string
     */
    static function checkBase64Extension($value)
    {
        $all_extensions = [
            'jpg', 'png', 'jpeg', 'pdf', 'docx', 'docm', 'dotx', 'dotm',
            'xlsx', 'xlsm', 'xltx', 'xltm', 'xlsb', 'xlam', 'pptx', 'pptm',
            'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm', 'thmx'
        ];
        $extension = explode(";", explode("/", $value)[1])[0];
        if (in_array($extension, $all_extensions)) {
            return '.' . $extension;
        }
        return '.jpg';
    }

    public function deleteFile($file)
    {

        $small = public_path().config('const.filePath.small').$file;
        $medium = public_path().config('const.filePath.medium').$file;
        $large = public_path().config('const.filePath.large').$file;
        $original = public_path().config('const.filePath.original').$file;

        if(file_exists($small)):
            @unlink($small);
        endif;

        if(file_exists($medium)):
            @unlink($medium);
        endif;

        if(file_exists($large)):
            @unlink($large);
        endif;

        if(file_exists($original)):
            @unlink($original);
        endif;
    }

    // ROTANA: GET MULTIPLE IMAGES
    public function myAllImageSizeToUrls($gallery, $disk = 'uploads', $useDefaultImage = false)
    {
        $g = [];
        if (is_array($gallery) && count($gallery)) {
            foreach ($gallery as $image) {
                if ($image) {
                    $g[] = self::myFileExist($image, $disk, $useDefaultImage);
                }
            }
        }
        return implode(",",$g);
    }

    /**
     * Get all image size
     *
     * @param string $profile
     * @param string $disk
     * @param boolean $useDefaultImage
     */
    public function myAllImageSize($profile, $disk = 'uploads', $useDefaultImage = true)
    {
        $d['small'] = $this->myFileExist($profile, $disk . '_small', $useDefaultImage);
        $d['medium'] = $this->myFileExist($profile, $disk . '_medium', $useDefaultImage);
        $d['large'] = $this->myFileExist($profile, $disk . '_large', $useDefaultImage);
        $d['original'] = $this->myFileExist($profile, $disk, $useDefaultImage);
        return $d;
    }

    /**
     * Check file
     *
     * @param string $profile
     * @param string $disk
     * @param boolean $useDefaultImage
     * @return string
     */
    public static function getUrl($file, $disk = 'public', $useDefaultImage = true)
    {
        if ($file) {
            if (env('APP_ENV') === 'local') {
                $urlFile = url("/storage".$file);
            } else {
                $urlFile = \Storage::disk($disk)->url($file);
            }
        }
        if ($useDefaultImage) {
            return $urlFile ?? url(config('const.filePath.default_image'));
        }
        return $urlFile;
    }
}
