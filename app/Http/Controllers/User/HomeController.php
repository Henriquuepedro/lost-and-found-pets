<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    private $banner;

    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
    }

    public function index()
    {
        // Recupera os banner
        $banners = $this->banner->getBanners();
        $arrBanners = array();
        foreach ( $banners as $banner ) {
            array_push($arrBanners, array(
                'path' => asset("user/img/banner/{$banner['path']}")
            ));
        }

        return view('user.home.index', compact('arrBanners'));
    }
}
