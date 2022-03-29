<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PictureBookController extends Controller
{
    public function frontPage()
    {
        return view('web-view.picture-book_front-page');
    }

    public function searchResult()
    {
        return view('web-view.picture-book_search-result');
    }

    public function flowerDetail()
    {
        return view('web-view.picture-book_flower-detail');
    }
}
