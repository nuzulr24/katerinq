<?php

namespace Modules\Seller\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;

use Modules\Seller\Entities\RekeningModel;
use Modules\Seller\Entities\RekeningBankModel as ListBank;
use Modules\User\Entities\OrderModel as Order;
use Modules\User\Entities\OrderHistoryModel as History;
use Modules\Seller\Entities\ProductModel as Product;

use App\Enums\GlobalEnum;

class SellerController extends Controller
{
    public function index()
    {
        $data = [
            'subtitle' => 'Halaman Utama',
        ];

        $getTotalOrders = History::where('seller_id', auth()->user()->id)->where('is_status', 1)->count();
        $getTotalOrdersCancel = History::where('seller_id', auth()->user()->id)->where('is_status', 4)->count();
        $getTotalWebsite = Product::where('user_id', auth()->user()->id)->count();
        $getListOrder = History::where('seller_id', auth()->user()->id)->orderBy('created_at', 'desc')->limit(10)->get();

        return view('seller::index', compact('data', 'getTotalOrders', 'getTotalOrdersCancel', 'getTotalWebsite', 'getListOrder'));
    }
}
