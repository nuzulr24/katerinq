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
use Modules\Seller\Entities\NotificationModel as Notificator;
use Modules\Seller\Entities\RekeningBankModel as ListBank;
use Modules\User\Entities\OrderModel as Order;
use Modules\Seller\Entities\WithdrawalModel as Withdrawal;
use Modules\Seller\Entities\ProductModel as Product;
use App\Models\Seller;
use App\Models\User;
use App\Models\LogActivites;

use App\Enums\GlobalEnum;
use App\Helpers\MailerHelper as Mailers;

class AccountController extends Controller
{
    public function index()
    {
        $data = [
            'subtitle' => 'Akun Saya'
        ];
        return view('seller::account.index', compact('data'));
    }

    public function preference()
    {
        $data = [
            'subtitle' => 'Preferensi'
        ];

        $getNotificationPreferences = Notificator::where('user_id', user()['id'])->where('notifyAs', GlobalEnum::isNotifyAsSeller)->first();
        return view('seller::account.preference', compact('data', 'getNotificationPreferences'));
    }

    public function updateProfile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'alias' => 'required',
            'nama' => 'required',
            'email' => 'required',
            'address' => 'required',
            'phone' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        $findUser = Seller::where('user_id', user()->id)->exists();

        if($findUser) {
            // update data
            $updateUser = Seller::where('user_id', user()->id)->first();
            $updateUser->name = $input['name'];
            $updateUser->alias = $input['alias'];
            $updateUser->address = $input['address'];
            $updateUser->phone = $input['phone'];
            $updateUser->description = $input['description'];

            $findAccount = User::where('id', user()->id)->first();
            $findAccount->name = $input['nama'];
            $findAccount->email = $input['email'];
            !empty($input['password']) ? $findAccount->password = bcrypt($input['password']) : '';

            // save data
            $updateUser->save();
            $findAccount->save();
            return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil disimpan'));
        } else {
            $seller = new Seller([
                'uuid' => Str::uuid(),
                'user_id' => user()->id,
                'name' => $input['name'],
                'alias' => $input['alias'],
                'is_active' => 1,
            ]);

            if ($seller->save()) {
                return redirect()->back()->with('swal', swal_alert('success', 'Anda berhasil mengkonfirmasi akun sebagai vendor party planner'));
            } else {
                return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan pada sistem!'));
            }
        }
    }

    public function updatePreference(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'updateProduct' => 'string',
            'updateNews' => 'string',
            'updateOrder' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        $findPreference = Notificator::where('user_id', user()->id)->where('notifyAs', GlobalEnum::isNotifyAsSeller)->first();
        $checkPreference = Notificator::where('user_id', user()->id)->where('notifyAs', GlobalEnum::isNotifyAsSeller)->count();

        if($checkPreference > 0) {
            // update data
            $findPreference->onUpdateProduct = $request->has('updateProduct') ? 1 : 0;
            $findPreference->onUpdateNews = $request->has('updateNews') ? 1 : 0;
            $findPreference->onUpdateOrders = $request->has('updateOrder') ? 1 : 0;

            // save data
            // dd($findPreference);

            $findPreference->save();
            return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil disimpan'));
        } else {
            $updateProduct = $request->has('updateProduct') ? 1 : 0;
            $updateNews = $request->has('updateNews') ? 1 : 0;
            $updateOrder = $request->has('updateOrder') ? 1 : 0;

            $post = new Notificator([
                'uid' => Str::uuid(),
                'user_id' => user()->id,
                'notifyAs' => GlobalEnum::isNotifyAsSeller,
                'onUpdateProduct' => $updateProduct,
                'onUpdateNews' => $updateNews,
                'onUpdateOrders' => $updateOrder
            ]);

            if ($post->save()) {
                return redirect()->back()->with('swal', swal_alert('success', 'Anda berhasil menambahkan data baru'));
            } else {
                return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan pada sistem!'));
            }
        }
    }

    // controller::for-rekening
    public function rekening(Request $request)
    {
        if ($request->ajax()) {
            $data = RekeningModel::where('user_id', auth()->user()->id)->select('*');
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title-post', function($row){

                    $getNameofBank = ListBank::where('id', $row->rid)->first()->nama;
                    $text = '
                    <p class="mb-0">' . $row->account_number . ' - ' . $getNameofBank . '</p>
                    ';
                    return $text;
                })
                ->addColumn('action', function($row){
                    $edit = route('rekening.edit', ['id' => $row->id]);
                    $delete = route('rekening.delete', ['id' => $row->id]);
                    $btn = '
                    <a href="' . $edit . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-pencil"></i></a>
                    <a data-url="' . $delete . '" href="#" class="btn btn-light btn-sm deleteContent px-4"><i class="ki-outline ki-trash"></i></a>
                    ';
                    return $btn;
                })
                ->addColumn('status', function($row){
                    if ($row->is_active == GlobalEnum::isRekeningActive) {
                        return '<span class="mb-1 badge font-medium bg-light-success text-success px-2 py-1">Aktif</span>';
                    } elseif($row->is_active == GlobalEnum::isRekeningInactive) {
                        return '<span class="mb-1 badge font-medium bg-light-primary text-primary px-2 py-1">Tidak Aktif</span>';
                    }
                })
                ->rawColumns(['title-post','action','status'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Semua Rekening',
            'button' => true,
            'module' => [
                'url' => site_url('seller', 'account/rekening/create'),
                'name' => 'Tambah baru'
            ]
        ];

        return view('seller::account.rekening.index', compact('data'));
    }

    public function createRekening()
    {
        $data = [
            'subtitle' => 'Tambah Baru'
        ];
        $listAllBank = ListBank::all();

        return view('seller::account.rekening.add', compact('data', 'listAllBank'));
    }

    public function storeRekening(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'account_number' => 'required|numeric',
            'rid' => 'required',
        ]);

        if($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan saat menambahkan rekening ' . $validator->errors()->first()))->withInput();
        }

        $input = $request->all();

        $post = new RekeningModel([
            'id' => Str::uuid(),
            'user_id' => auth()->user()->id,
            'account_number' => $input['account_number'],
            'rid' => $input['rid'],
            'name' => $input['name'],
            'is_active' => 1
        ]);

        $check = RekeningModel::where('account_number', $input['account_number'])->count();
        if($check == 0) {
            if($post->save()) {
                return redirect()->to(site_url('seller', 'account/rekening'))->with('swal', swal_alert('success', 'Anda berhasil menambahkan rekening.'));
            } else {
                return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan saat menambahkan rekening.'));
            }
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Ditemukan data yang telah ada'));
        }
    }

    public function editRekening($id)
    {
        $data = [
            'subtitle' => 'Edit'
        ];

        $listAllBank = ListBank::all();
        $getDetailAccount = RekeningModel::find($id);

        return view('seller::account.rekening.edit', compact('data', 'listAllBank', 'getDetailAccount'));
    }

    public function updateRekening(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'rid' => 'required',
            'account_number' => 'required|numeric',
            'is_active' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', $validator->errors()->first()))->withInput();
        }

        $input = $request->all();
        $account = RekeningModel::find($id);

        if($account) {
            $account->name = $input['name'];
            $account->rid = $input['rid'];
            $account->account_number = $input['account_number'];
            $account->is_active = $input['is_active'];
            $account->save();

            return redirect()->to(site_url('seller', 'account/rekening'))->with('swal', swal_alert('success', 'Berhasil memperbarui rekening.'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Rekening tidak ditemukan.'));
        }
    }

    public function removeRekening($id)
    {
        $account = RekeningModel::find($id);
        if($account) {
            $account->delete();
            return redirect()->to(site_url('seller', 'account/rekening'))->with('swal', swal_alert('success', 'Berhasil menghapus data.'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Site tidak ditemukan.'));
        }
    }

    public function withdrawal(Request $request)
    {
        if ($request->ajax()) {
            $data = Withdrawal::where('user_id', auth()->user()->id)->select('id', 'invoice_id', 'user_id', 'amount', 'is_status', 'is_account');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('invoice-number', function($row){
                    $text = '
                    <p class="fw-bold mb-n1">#' . $row->invoice_id . '</p>
                    ';
                    return $text;
                })
                ->addColumn('account', function($row){
                    $checkBank = ListBank::where('id', $row->rekening->first()->rid)->first();
                    $checkUserBank = RekeningModel::where('id', $row->is_account)->first();
                    $text = '
                    <p class="fw-bold mb-0">' . $checkUserBank->account_number . '</p>
                    <p class="small text-gray-400 mb-n1">' . $checkUserBank->name . ' / ' . $checkBank->nama . '</p>
                    ';

                    return $text;

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
                ->rawColumns(['invoice-number','account','status','price'])
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
            'subtitle' => 'Penarikan',
            'button' => true,
            'module' => [
                'url' => site_url('seller', 'account/withdrawal/create'),
                'name' => 'Tarik Tunai'
            ]
        ];

        return view('seller::account.withdraw.index', compact('data'));
    }

    public function createRequest()
    {
        $data = [
            'subtitle' => 'Tarik Tunai',
        ];

        $getListOfBank = RekeningModel::where('user_id', user()->id)->get();
        return view('seller::account.withdraw.add', compact('data', 'getListOfBank'));
    }

    public function storeRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'is_account' => 'required'
        ]);

        if($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', $validator->errors()->first()))->withInput();
        }

        $input = $request->all();
        $invoice_number = invoiceGenerator();

        $post = new Withdrawal([
            'id' => Str::uuid(),
            'invoice_id' => $invoice_number,
            'user_id' => user()->id,
            'amount' => $input['amount'],
            'is_status' => GlobalEnum::isWithdrawPending,
            'is_account' => $input['is_account']
        ]);

        if(user()->income < $input['amount']) {
            return redirect()->back()->with('swal', swal_alert('error', 'Saldo tidak mencukupi untuk melakukan penarikan.'));
        }

        if($post->save())
        {
            LogActivites::default([
                'causedBy' => user()->id,
                'logType' => GlobalEnum::LogOfGeneral,
                'withContent' => [
                    'status' => 'minus',
                    'text' => 'Anda berhasil melakukan permintaan tarik tunai pada tanggal ' . date('Y-m-d H:i:s'),
                ]
            ]);
            return redirect()->to(site_url('seller', 'account/withdrawal'))->with('swal', swal_alert('success', 'Anda berhasil melakukan permintaan tarik tunai.'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan/data telah ada pada sistem.'));
        }
    }

    public function viewRequest($invoice)
    {
        $payment = Withdrawal::find($invoice);
        if($payment) {
            $data = [
                'subtitle' => 'Detil Penarikan',
            ];

            return view('seller::account.withdraw.detail', compact('data', 'payment'));
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
                        'text' => 'Anda berhasil melakukan pembatalan tarik tunai dengan kode invoice ' . $payment->invoice_id .' pada tanggal ' . date('Y-m-d H:i:s'),
                    ]
                ]);
                return redirect()->to(site_url('seller', 'account/withdrawal'))->with('swal', swal_alert('success', 'Anda berhasil melakukan pembatalan tarik tunai.'));
            } else {
                return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan saat melakukan pembatalan penarikan.'));
            }
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Data tidak ditemukan.'));
        }
    }
}
