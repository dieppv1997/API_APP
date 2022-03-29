<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class TermsController extends Controller
{
    /**
     * @return Factory|View
     */
    public function terms()
    {
        return view('terms.index');
    }
}
