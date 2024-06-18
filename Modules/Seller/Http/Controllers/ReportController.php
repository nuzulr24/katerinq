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
use Modules\User\Entities\OrderHistoryModel as Order;
use Modules\Seller\Entities\WithdrawalModel as Withdrawal;
use App\Models\LogActivites;
use WithPagination;

use App\Enums\GlobalEnum;

class ReportController extends Controller
{

    public function index()
    {
        $getTotalIncomeOrder = Order::where('seller_id', user()->id)->sum('price');
        $getTotalPendingIncomeOrder = Order::where('seller_id', user()->id)->where('is_status', 1)->sum('price');
        $getListOrder = Order::where('seller_id', user()->id)->orderBy('created_at', 'desc')->limit(5)->get();
        $getFlowOrder = Order::getTotalIncomeSeller();
        $getTotalOrder = Order::where('seller_id', user()->id)->count();
        $getListAwaitingWithdrawal = Withdrawal::where('is_status', 1)->where('user_id', user()->id)->count();

        $data = [
            'subtitle' => 'Keuangan dan Transaksi'
        ];
        return view('seller::reports.index', compact('data', 'getTotalIncomeOrder', 'getTotalPendingIncomeOrder', 'getListOrder', 'getFlowOrder', 'getListAwaitingWithdrawal', 'getTotalOrder'));
    }

    public function statistic(Request $request)
    {
        $logsActivity = LogActivites::where('logType', GlobalEnum::LogOfLogin)->where('causedBy', user()->id)->orderBy('created_at', 'desc')->limit(5)->get();
        $logGeneral = LogActivites::where('logType', GlobalEnum::LogOfGeneral)->where('causedBy', user()->id)->orderBy('created_at', 'desc')->limit(5)->get();

        $data = [
            'subtitle' => 'Riwayat Aktifitas'
        ];
        return view('seller::reports.statistic', compact('data', 'logsActivity', 'logGeneral'));
    }
}
