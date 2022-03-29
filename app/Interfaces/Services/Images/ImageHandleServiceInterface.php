<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Interfaces\Services\Images;

interface ImageHandleServiceInterface
{
    /**
     * @param $file
     * @param $path
     * @return string|false
     */
    public function uploadImage($file, $path);

    public function deleteImage($filePath);
}
