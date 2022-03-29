<?php
// project hash key
// e921d896b24ee51ad95dc303a034758da1187532b0a44189bd99aa333255781b

namespace App\Services\Images;

use App\Exceptions\ServerException;
use App\Interfaces\Services\Images\ImageHandleServiceInterface;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImageHandleService
 * @package App\Services
 */
class ImageHandleService extends BaseService implements ImageHandleServiceInterface
{
    /**
     * @param $file
     * @param $path
     * @return string
     * @throws ServerException
     */
    public function uploadImage($file, $path): string
    {
        $uploadImage = Storage::disk('s3')->putFileAs(dirname($path), $file, basename($path));
        if (!$uploadImage) {
            throw new ServerException();
        }
        return $uploadImage;
    }

    /**
     * @param $filePath
     * @return bool
     */
    public function deleteImage($filePath): bool
    {
        return Storage::disk('s3')->delete($filePath);
    }
}
