<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;

use Modules\Seller\Entities\RekeningModel;
use Modules\Seller\Entities\RekeningBankModel as ListBank;
use Modules\User\Entities\OrderModel as Order;
use Modules\Seller\Entities\ProductModel as Product;
use Modules\Seller\Entities\WithdrawalModel as Withdrawal;
use App\Models\LogActivites;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'subtitle' => 'Halaman Utama',
        ];

        $getTotalOrders = Order::count();
        $getTotalWebsite = Product::count();
        $getListAwaitingWithdrawal = Withdrawal::where('is_status', 1)->count();
        $getIncomeOrder = Order::where('is_status', 5)->sum('price');
        $getListOrder = Order::orderBy('created_at', 'desc')->limit(10)->get();
        $getFlowOrder = Order::getTotalAmountByMonth();
        $getListActivity = LogActivites::orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.app.dashboard.index', compact('data', 'getTotalOrders', 'getTotalWebsite', 'getListAwaitingWithdrawal', 'getIncomeOrder', 'getListOrder', 'getFlowOrder', 'getListActivity'));
    }
}
