<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// additional library loaded
use App\Enums\GlobalEnum;
use App\Models\Seller;
use App\Models\LogActivites;
use Modules\User\Entities\OrderModel as Order;
use Modules\Seller\Entities\AccountModel as Account;

class UserController extends Controller
{
    public function index()
    {
        $data = [
            'subtitle' => 'Halaman Utama',
        ];

        $getTotalOrders = Order::where('user_id', user()->id)->count();
        $getListOrder = Order::where('user_id', user()->id)->orderBy('created_at', 'desc')->limit(5)->get();
        $getListPengeluaran = Order::where('user_id', user()->id)->where('is_status', 5)->orderBy('created_at', 'desc')->sum('price');

        return view('user::index', compact('data', 'getTotalOrders', 'getListOrder', 'getListPengeluaran'));
    }
}
