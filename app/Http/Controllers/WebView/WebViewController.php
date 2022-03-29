<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\WebView;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\WebView\ApiWebViewServiceInterface;
use Illuminate\Http\Request;

class WebViewController extends Controller
{
    protected $webViewService;

    /**
     * WebViewController constructor.
     * @param ApiWebViewServiceInterface $webViewService
     */
    public function __construct(ApiWebViewServiceInterface $webViewService)
    {
        $this->webViewService = $webViewService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CheckAuthenticationException
     * @throws \App\Exceptions\CheckAuthorizationException
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\QueryException
     * @throws \App\Exceptions\ServerException
     * @throws \App\Exceptions\UnprocessableEntityException
     */
    public function pictureBook(Request $request)
    {
        return $this->getData(function () {
            return $this->webViewService->getPictureBookWebViewUrl();
        }, $request);
    }
}
