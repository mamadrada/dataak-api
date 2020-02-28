<?php
namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use File;

trait UploadImageTrait
{
    public function uploadImage($image, $folder='', $fileName, $resize = 0, $width = '', $height = '')
    {
        $basePath = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$folder;
        File::makeDirectory($basePath, 0777, true, true);
        $name = createSlug($fileName).'-'.time().'.'. $image->getClientOriginalExtension();

        $fileRealPath = $basePath.DIRECTORY_SEPARATOR.$name;
        $img =  Image::make($image);
        $img->backup();
        if ($resize) {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })->save($fileRealPath);
        } else {
            $img->save($fileRealPath);
        }
        $fileUrl = env('APP_URL').DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$name;
        return $fileUrl;
    }
}
