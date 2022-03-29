<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Services\WebView;

use App\Interfaces\Services\WebView\ApiWebViewServiceInterface;

class ApiWebViewService implements ApiWebViewServiceInterface
{
    public function getPictureBookWebViewUrl()
    {
        return [
            'data' => [
                'name' => 'Picture book',
                'url' => config('settings.webViewUrl.pictureBook')
            ]
        ];
    }
}
