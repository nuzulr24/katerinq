<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Carbon\Carbon;

// additional library loaded
use App\Enums\GlobalEnum;
use App\Helpers\MailerHelper as Mailers;

// additional model binding
use Modules\User\Entities\OrderModel as Order;
use App\Models\User;
use Modules\User\Entities\OrderHistoryModel as OrderHistory;
use Modules\Seller\Entities\BillingModel as Billing;
use Modules\Seller\Entities\AccountModel as Account;
use Modules\Seller\Entities\PaymentModel as Payment;
use Modules\Seller\Entities\WithdrawalModel as Withdrawal;
use Modules\Seller\Entities\RekeningBankModel as ListBank;
use Modules\Seller\Entities\RekeningModel;
use App\Models\LogActivites;

class OrdersController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::select('*')->orderBy('created_at', 'desc');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('invoice', function($row){
                    $text = '
                    <p class="fw-bold">#' . $row->invoice_number . '</p>
                    ';
                    return $text;
                })
                ->addColumn('author', function($row){
                    return User::where('id', $row->user_id)->first()->name;
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
                ->addColumn('total', function($row){
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
                ->rawColumns(['status', 'invoice', 'author', 'total'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Semua Pesanan'
        ];

        return view('admin.app.transaction.orders.index', compact('data'));
    }

    public function view($invoice)
    {
        $check = Order::where('id', $invoice)->first();
        if($check)
        {
            $data = [
                'subtitle' => 'Detil Pesanan'
            ];

            $getInfoOrders = $check;
            return view('admin.app.transaction.orders.detail', compact('data', 'getInfoOrders'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan data yang anda cari'));
        }
    }

    public function billing(Request $request)
    {
        if ($request->ajax()) {
            $data = Billing::select('*')->orderBy('created_at', 'DESC');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('ticket-invoice', function($row){
                    $text = $row->invoice_id;
                    return $text;
                })
                ->addColumn('author', function($row){
                    $checkInOrder = Order::where('invoice_number', $row->invoice_id)->first();
                    if($checkInOrder) {
                        return '
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-25px symbol-circle">
                                <div class="symbol-label" style="background-image:url(' . gravatar_team(findUser($checkInOrder->buy_id)->email) . ')"></div>
                            </div>
                            <div class="ms-3"><span>' . findUser($checkInOrder->buy_id)->name . '</span></div>
                        </div>
                        ';
                    } else {
                        $checkInDeposit = Payment::where('deposit_number', $row->invoice_id)->first();
                        if($checkInDeposit) {
                            return '
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-25px symbol-circle">
                                    <div class="symbol-label" style="background-image:url(' . gravatar_team(findUser($checkInDeposit->user_id)) . ')"></div>
                                </div>
                                <div class="ms-3"><span>' . findUser($checkInDeposit->user_id)->name . '</span></div>
                            </div>
                            ';
                        }
                    }
                })
                ->addColumn('status', function($row){
                    $checkInOrder = Order::where('invoice_number', $row->invoice_id)->first();
                    if($checkInOrder) {
                        if($checkInOrder->is_status == GlobalEnum::isOrderCompleted) {
                            return '<span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7"><i class="ki-outline ki-check fs-6 me-2 text-success"></i>Tuntas</span>';
                        } else {
                            return '<span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7"><i class="ki-outline ki-cross fs-6 me-2 text-danger"></i>Belum</span>';
                        }
                    } else {
                        $checkInDeposit = Payment::where('deposit_number', $row->invoice_id)->first();
                        if($checkInDeposit) {
                            if($checkInDeposit->is_status == GlobalEnum::isDepositPaid) {
                                return '<span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7"><i class="ki-outline ki-check fs-6 me-2 text-success"></i>Tuntas</span>';
                            } else {
                                return '<span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7"><i class="ki-outline ki-cross fs-6 me-2 text-danger"></i>Belum</span>';
                            }
                        }
                    }
                })
                ->addColumn('type', function($row){
                    $checkInOrder = Order::where('invoice_number', $row->invoice_id)->first();
                    if($checkInOrder) {
                        return 'Transaksi Umum';
                    } else {
                        $checkInDeposit = Payment::where('deposit_number', $row->invoice_id)->first();
                        if($checkInDeposit) {
                            return 'Deposit';
                        }
                    }
                })
                ->addColumn('price', function($row){
                    $checkInOrder = Order::where('invoice_number', $row->invoice_id)->first();
                    if($checkInOrder) {
                        return 'Rp. ' . number_format($checkInOrder->price, 0, ',', '.');
                    } else {
                        $checkInDeposit = Payment::where('deposit_number', $row->invoice_id)->first();
                        if($checkInDeposit) {
                            return 'Rp. ' . number_format($checkInDeposit->amount, 0, ',', '.');
                        }
                    }
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $search = $request->get('search')['value'];
                        $query->where('invoice_id', 'LIKE', "%$search%");
                    }
                })
                ->rawColumns(['type','ticket-invoice','author','status','price'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Semua Tagihan'
        ];

        return view('admin.app.transaction.billing.index', compact('data'));
    }

    public function withdrawal(Request $request)
    {
        if ($request->ajax()) {
            $data = Withdrawal::select('id', 'invoice_id', 'user_id', 'amount', 'is_status', 'is_account', 'created_at');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('invoice-number', function($row){
                    $text = '
                    <p class="fw-bold mb-0">#' . $row->invoice_id . '</p>
                    <p class="small text-gray-400 mb-0">Dibuat pada ' . date('j F Y', strtotime($row->created_at)) . '</p>
                    ';
                    return $text;
                })
                ->addColumn('account', function($row){
                    if(!empty($row->rekening->first())) {
                        $checkBank = ListBank::where('id', $row->rekening->first()->rid)->first();
                        $text = '
                        <span class="mb-1 badge font-medium bg-light-primary text-primary py-3 px-4 fs-7">' . $checkBank->nama . '</p>
                        ';
                        return $text;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('author', function($row){
                    return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-25px symbol-circle">
                            <div class="symbol-label" style="background-image:url(' . gravatar_team($row->user->first()->email) . ')"></div>
                        </div>
                        <div class="ms-3"><span>' . $row->user->first()->name . '</span></div>
                    </div>
                    ';
                })
                ->addColumn('status', function($row){
                    if ($row->is_status == GlobalEnum::isWithdrawPending) {
                        return '<span class="mb-1 badge font-medium bg-light-warning text-warning py-3 px-4 fs-7">Menunggu Pembayaran</span>';
                    } elseif($row->is_status == GlobalEnum::isWithdrawOnProgress) {
                        return '<span class="mb-1 badge font-medium bg-light-primary text-primary py-3 px-4 fs-7">Sedang Proses</span>';
                    } elseif($row->is_status == GlobalEnum::isWithdrawPaid) {
                        return '<span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7">Dibayarkan</span>';
                    } elseif($row->is_status == GlobalEnum::isWithdrawCancel) {
                        return '<span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7">Dibatalkan</span>';
                    }
                })
                ->addColumn('price', function($row){
                    return 'Rp. ' . number_format($row->amount, 0, ',', '.');
                })
                ->rawColumns(['invoice-number','account','status','price','author'])
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
                        } elseif($filterCategory[0] === 'invoice') {
                            if(!empty($filterCategory[1])) {
                                $query->where('invoice_id', 'LIKE', "%$filterCategory[1]%");
                            } else {
                                $query->get();
                            }
                        }
                    }
                })
                ->make(true);
        }

        $data = [
            'subtitle' => 'Daftar Penarikan'
        ];

        return view('admin.app.transaction.withdrawal.index', compact('data'));
    }

    public function storeRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,svg|max:7048',
        ], [
            'image.mimes' => 'Tipe file yang diunggah harus jpg, jpeg, png, atau svg.',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withInput()->with('swal', swal_alert('error', 'Unexpected error, please try again. code: ' . $validator->errors()->first()));
        }

        $input = $request->all();
        $withdraw = Withdrawal::find($input['id']);

        if($withdraw) {
            // Jika ada file baru yang diunggah, simpan file baru di storage
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $foto_namaBaru = $request->file('image')->store('public/images');
                $withdraw->is_attachment = $foto_namaBaru;
            }

            $withdraw->is_status = GlobalEnum::isWithdrawPaid;

            if($withdraw->save()) {
                $users = Account::where('id', $withdraw->user_id)->first();
                $users->update([
                    'income' => $users->income - $withdraw->amount
                ]);

                LogActivites::default([
                    'causedBy' => $withdraw->user_id,
                    'logType' => GlobalEnum::LogOfGeneral,
                    'withContent' => [
                        'status' => 'add',
                        'text' => 'Anda berhasil menerima penarikan saldo sejumlah ' . rupiah_changer($withdraw->amount) . ' pada tanggal ' . date('Y-m-d H:i:s'),
                    ]
                ]);
                return redirect()->back()->with('swal', swal_alert('success', 'Anda berhasil melakukan konfirmasi penarikan tarik tunai.'));
            } else {
                return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan/data pada saat menyimpan data.'));
            }
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan/data tidak ditemukan pada sistem.'));
        }
    }

    public function viewRequest($invoice)
    {
        $payment = Withdrawal::find($invoice);
        if($payment) {
            $data = [
                'subtitle' => 'Detil Penarikan',
            ];

            return view('admin.app.transaction.withdrawal.detail', compact('data', 'payment'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Data tidak ditemukan.'));
        }
    }

    public function cancelRequest($invoice)
    {
        $payment = Withdrawal::find($invoice);
        if($payment) {

            $payment->is_status = GlobalEnum::isWithdrawCancel;
            if($payment->save()) {
                LogActivites::default([
                    'causedBy' => user()->id,
                    'logType' => GlobalEnum::LogOfGeneral,
                    'withContent' => [
                        'status' => 'minus',
                        'text' => 'Admin melakukan pembatalan tarik tunai dengan kode invoice ' . $payment->invoice_id .' pada tanggal ' . date('Y-m-d H:i:s'),
                    ]
                ]);
                return redirect()->back()->with('swal', swal_alert('success', 'Anda berhasil melakukan pembatalan tarik tunai.'));
            } else {
                return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan saat melakukan pembatalan penarikan.'));
            }
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Data tidak ditemukan.'));
        }
    }

    public function cetak(Request $request)
    {
        $start_date = $request->start_date ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $end_date = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $report = Order::whereRaw('DATE(created_at) BETWEEN ? AND ?', [$start_date, $end_date]);
        $periode = $start_date . ' s/d ' . $end_date;
        return view('admin.app.transaction.orders.cetak', compact('report', 'periode'));
    }
}
