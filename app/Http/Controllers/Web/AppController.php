<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Exceptions\BadRequestException;

class AppController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View
     * @throws BadRequestException
     */
    public function launchApp(Request $request)
    {
        $params = $request->only([
            'type',
            'token',
            'email',
        ]);
        if (count($params) != 3) {
            throw new BadRequestException('Bad Request');
        }
        $deepLink = config('settings.email.deeplink_domain');
        $androidAppUrl = config('settings.appUrl.android');
        $iosAppUrl = config('settings.appUrl.ios');
        return view('app.launch-app', [
            'androidAppUrl' => $androidAppUrl,
            'iosAppUrl' => $iosAppUrl,
            'rootUrl' => "{$deepLink}{$params['type']}",
            'token' => $params['token'],
            'email' => $params['email']
        ]);
    }
}
