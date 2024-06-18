<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DataTables;

// additional library loaded
use App\Enums\GlobalEnum;
use App\Helpers\MailerHelper as Mailers;

// additional model binding
use Modules\User\Entities\OrderModel as Order;
use Modules\User\Entities\OrderHistoryModel as OrderHistory;
use Modules\Seller\Entities\BillingModel as Billing;
use App\Models\User;
use App\Models\LogActivites;

class TransactionsController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::where('user_id', auth()->user()->id)->orderBy('created_at', 'DESC');
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('invoice', function($row){
                    $text = '
                    <p class="fw-bold">#' . $row->invoice_number . '</p>
                    ';
                    return $text;
                })
                ->addColumn('status', function($row){
                    if ($row->is_status == 1) {
                        return '<span class="mb-1 badge font-medium bg-light-dark text-dark py-3 px-4 fs-7 text-center">Pending</span>';
                    } elseif($row->is_status == 2) {
                        return '<span class="mb-1 badge font-medium bg-light-primary text-primary py-3 px-4 fs-7 text-center">Dalam pengerjaan</span>';
                    } elseif($row->is_status == 3) {
                        return '<span class="mb-1 badge font-medium bg-light-info text-info py-3 px-4 fs-7 text-center">Dikirim</span>';
                    } elseif($row->is_status == 4) {
                        return '<span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7 text-center">Dibatalkan</span>';
                    } elseif($row->is_status == 5) {
                        return '<span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7 text-center">Selesai</span>';
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
                                $query->where('is_status', '=', $filterCategory[1]);
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
                ->rawColumns(['invoice', 'status','harga'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Semua Pesanan'
        ];

        return view('user::transaction.orders.index', compact('data'));
    }

    public function view($invoice)
    {
        $check = Order::where('id', $invoice)->where('user_id', auth()->user()->id)->first();
        if($check)
        {
            $data = [
                'subtitle' => 'Detil Pesanan'
            ];

            $getInfoOrders = $check;
            return view('user::transaction.orders.detail', compact('data', 'getInfoOrders'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan data yang anda cari'));
        }
    }

    public function cancelOrder(Request $request, $invoice)
    {
        $check = Order::where('id', $invoice)->where('user_id', auth()->user()->id)->first();
        if($check)
        {
            $check->is_status = 4;
            $check->url_payment = NULL;
            $check->save();

            $code = $check->invoice_number;

            DB::statement("UPDATE tbl_order_detail SET is_status = 4 where order_id = '$code'");
            return redirect()->back()->with('swal', swal_alert('success', 'Pesanan anda telah dibatalkan'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan data yang anda cari'));
        }
    }

    public function complete(Request $request, $invoice)
    {
        $check = Order::where('id', $invoice)->where('user_id', auth()->user()->id)->first();
        if($check)
        {
            $check->is_status = 5;
            $check->url_payment = NULL;
            $check->save();

            $getDetail = OrderHistory::where('order_id', $check->invoice_number)->get();
            foreach($getDetail as $items) {
                if($items->is_status == 3) {
                    $price = $items->price;
                    $seller_id = $items->seller_id;
                    $id = $items->id;
                    $update = DB::statement("UPDATE tbl_order_detail SET is_status = '3' where id = '$id'");
                    $save = DB::statement("UPDATE tbl_users SET income = income + $price where id = '$seller_id'");
                    if($save && $update) {
                        return redirect()->back()->with('swal', swal_alert('success', 'Pesan telah anda terima dan sesuai permintaan anda.'));
                    } else {
                        return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan saat melakukan penambahan saldo.'));
                    }
                } else {
                    return redirect()->back()->with('swal', swal_alert('error', 'Terdapat pesanan yang belum selesai'));
                }
            }
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan data yang anda cari'));
        }
    }

    public function billing(Request $request)
    {
        if ($request->ajax()) {
            $data = Billing::where('buy_id', auth()->user()->id)->select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('ticket-invoice', function($row){
                    $text = '
                    <p class="fw-bold">#' . $row->invoice_number . '</p>
                    ';
                    return $text;
                })
                ->addColumn('title-post', function($row){
                    $text = '
                    <p class="mb-0 fw-bold">' . removeUrlPrefix($row->website_url) . '</p>
                    <p class="mb-0 small text-muted">Terakhir diperbarui pada ' . date_formatting($row->updated_at, 'timeago') . '</p>
                    ';
                    return $text;
                })
                ->addColumn('status', function($row){
                    if ($row->is_status == GlobalEnum::isOrderRequested) {
                        return '<span class="mb-1 badge font-medium bg-dark text-white py-3 px-4 fs-7">Menunggu</span>';
                    } elseif($row->is_status == GlobalEnum::isOrderOnWorking) {
                        return '<span class="mb-1 badge font-medium bg-primary text-white py-3 px-4 fs-7">Dalam pengerjaan</span>';
                    } elseif($row->is_status == GlobalEnum::isOrderSubmitted) {
                        return '<span class="mb-1 badge font-medium bg-danger text-white py-3 px-4 fs-7">Dikirim</span>';
                    } elseif($row->is_status == GlobalEnum::isOrderCompleted) {
                        return '<span class="mb-1 badge font-medium bg-success text-white py-3 px-4 fs-7">Selesai</span>';
                    } elseif($row->is_status == GlobalEnum::isOrderReqCancel) {
                        return '<span class="mb-1 badge font-medium bg-danger text-white py-3 px-4 fs-7">Permintaan Ditolak</span>';
                    } elseif($row->is_status == GlobalEnum::isOrderCancelled) {
                        return '<span class="mb-1 badge font-medium bg-danger text-white py-3 px-4 fs-7">Dibatalkan</span>';
                    } elseif($row->is_status == GlobalEnum::isOrderRejected) {
                        return '<span class="mb-1 badge font-medium bg-danger text-white py-3 px-4 fs-7">Ditolak</span>';
                    }
                })
                ->addColumn('price', function($row){
                    return 'Rp. ' . number_format($row->total, 0, ',', '.');
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $search = $request->get('search')['value'];

                        $filterCategory = explode('|', $search);
                        if($filterCategory[0] === 'status') {
                            if(!empty($filterCategory[1])) {
                                $query->where('is_status', '=', $filterCategory[1]);
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
                ->rawColumns(['ticket-invoice','title-post','status','price'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Semua Tagihan'
        ];

        return view('user::transaction.billing.index', compact('data'));
    }

    public function return(Request $request)
    {
        $payment = Order::where('invoice_number', $request->input('merchantOrderId'))->get()->first();
        if($payment) {
            if($request->input('statusCode') == 00) {
                return redirect()->to(site_url('user', 'orders'))->with('swal', swal_alert('success', 'Anda kembali ke laman detail deposit.'));
            } else {
                return redirect()->to(site_url('user', 'orders'))->with('swal', swal_alert('error', 'Terjadi kesalahan saat melakukan penambahan saldo.'));
            }
        } else {
            return redirect()->to(site_url('user', 'orders'))->with('swal', swal_alert('error', 'Tidak ditemukan data.'));
        }
    }
}
