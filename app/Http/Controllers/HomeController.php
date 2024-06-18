<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

// other module binding
use App\Enums\GlobalEnum as Status;

// model binding
use Modules\User\Entities\OrderModel as Order;
use Modules\User\Entities\ReviewModel as Reviews;
use Modules\Seller\Entities\ProductModel as Product;
use Modules\Seller\Entities\AccountModel as Account;
use App\Models\Content;
use App\Models\Pages;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'subtitle' => 'Platform Vendor Party Planner'
        ];

        // additional binding items
        $getAllPost = Content::where('is_status', Status::isPostPublished)->select('*')->limit(4)->get();

        return view('landing.index', compact('data', 'getAllPost'));
    }

    public function closeGuide()
    {
        if(user()->is_close_guide != null)
        {
            $account = new Account();
            $account->is_close_guide = 1;
            $account->save();
            echo 'sudah update';
        }
    }

    public function howToSell()
    {
        $data = [
            'subtitle' => 'Cara Bergabung sebagai Vendor'
        ];

        return view('landing.market.sell', compact('data'));
    }

    public function market()
    {
        $data = [
            'subtitle' => 'Produk Vendor by Party Planner'
        ];

        return view('landing.market.listing', compact('data'));
    }

    public function productDetail($uuid)
    {
        $sites = Sites::find($uuid);

        if($sites) {
            $data = [
                'subtitle' => 'Tentang Situs ' . removeUrlPrefix($sites->url)
            ];

            $getAllReviews = Reviews::where('website_id', $sites->id)->where('review', '!=', 'Auto rated by system')->orderBy('created_at', 'desc')->limit(5)->get();

            return view('landing.market.detail', compact('data', 'sites', 'getAllReviews'));
        } else {
            return redirect()->route('marketplace')->with('swal', swal_alert('error', 'Tidak ditemukan data produk...'));
        }
    }

    public function userDetail($uuid)
    {
        $users = Sites::find($uuid);

        if($users) {
            $data = [
                'subtitle' => 'Tentang ' . $users->name
            ];

            return view('landing.market.detailUser', compact('data', 'users'));
        } else {
            return redirect()->route('marketplace')->with('swal', swal_alert('error', 'Tidak ditemukan data produk...'));
        }
    }

    public function pages($slug)
    {
        $pages = Pages::where('slug', $slug)->first();

        if($pages) {
            $data = [
                'subtitle' => $pages->title
            ];

            return view('landing.pages.detail', compact('data', 'pages'));
        } else {
            return redirect()->route('landing')->with('swal', swal_alert('error', 'Tidak ditemukan laman...'));
        }
    }

    public function about()
    {
        $data = [
            'subtitle' => 'Tentang Kami'
        ];

        return view('landing.about', compact('data'));
    }

    public function blog()
    {
        $data = [
            'subtitle' => 'Informasi'
        ];

        return view('landing.blog.index', compact('data'));
    }

    public function blogDetail($slug)
    {
        $blog = Content::where('slug', $slug)->first();

        if($blog) {
            $data = [
                'subtitle' => 'Detil Informasi'
            ];

            return view('landing.blog.detail', compact('data', 'blog'));
        } else {
            return redirect()->route('landing')->with('swal', swal_alert('error', 'Tidak ditemukan laman...'));
        }
    }
}
