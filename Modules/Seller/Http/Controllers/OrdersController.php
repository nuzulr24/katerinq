<?php

namespace Modules\Seller\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;

// additional library loaded
use App\Enums\GlobalEnum;
use App\Helpers\MailerHelper as Mailers;

// additional model binding
use Modules\User\Entities\OrderModel as Order;
use Modules\User\Entities\OrderHistoryModel as OrderHistory;
use Modules\Seller\Entities\BillingModel as Billing;
use Modules\Seller\Entities\AccountModel as Account;
use Modules\Seller\Entities\PaymentModel as Payment;
use App\Models\User;

use App\Models\LogActivites;

class OrdersController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = OrderHistory::where('seller_id', user()->id)->select('*')->orderBy('created_at', 'DESC');
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('pemesan', function($row){
                    return User::where('id', $row->buy_id)->first()->name;
                })
                ->addColumn('invoice', function($row){
                    $text = '
                    <p class="fw-bold">#' . $row->order_id . '</p>
                    ';
                    return $text;
                })
                ->addColumn('status', function($row){
                    if ($row->is_status == 1) {
                        return '<span class="mb-1 badge font-medium bg-light-dark text-dark py-3 px-4 fs-7 text-center">Pending</span>';
                    } elseif($row->is_status == 2) {
                        return '<span class="mb-1 badge font-medium bg-light-primary text-primary py-3 px-4 fs-7 text-center">Dalam pengerjaan</span>';
                    } elseif($row->is_status == 3) {
                        return '<span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7 text-center">Selesai</span>';
                    } elseif($row->is_status == 4) {
                        return '<span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7 text-center">Dibatalkan</span>';
                    }
                })
                ->addColumn('harga', function($row){
                    return 'Rp. ' . number_format($row->price, 0, ',', '.');
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $search = $request->get('search')['value'];

                        $filterCategory = explode('|', $search);
                        if($filterCategory[0] === 'status') {
                            if(!empty($filterCategory[1])) {
                                $query->where('is_status', $filterCategory[1]);
                            } else {
                                $query->get();
                            }
                        } elseif($filterCategory[0] === 'orders') {
                            if(!empty($filterCategory[1])) {
                                $query->where('invoice_number', 'LIKE', "%$filterCategory[1]%");
                            } else {
                                $query->get();
                            }
                        }
                    }
                })
                ->rawColumns(['invoice', 'status','harga', 'pemesan'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Semua Pesanan'
        ];

        return view('seller::orders.index', compact('data'));
    }

    public function view($invoice)
    {
        $check = OrderHistory::where('order_id', $invoice)->first();
        if($check)
        {
            $data = [
                'subtitle' => 'Detil Pesanan'
            ];

            $getInfoOrders = $check;
            return view('seller::orders.detail', compact('data', 'getInfoOrders'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan data yang anda cari'));
        }
    }

    public function complete(Request $request, $invoice)
    {
        $check = OrderHistory::where('order_id', $invoice)->where('seller_id', user()->id)->first();
        if($check)
        {
            $check->is_status = 3;
            $check->save();

            return redirect()->back()->with('swal', swal_alert('success', 'Anda telah berhasil memperbarui status'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan data yang anda cari'));
        }
    }

    public function working(Request $request, $invoice)
    {
        $check = OrderHistory::where('order_id', $invoice)->where('seller_id', user()->id)->first();
        if($check)
        {
            $check->is_status = 2;
            $check->save();

            return redirect()->back()->with('swal', swal_alert('success', 'Anda telah berhasil memperbarui status'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan data yang anda cari'));
        }
    }
}
