<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;

// additional library loaded
use App\Enums\GlobalEnum;
use Carbon\Carbon;
use App\Helpers\MailerHelper as Mailers;

// additional modules model binding
use Modules\Seller\Entities\NotificationModel as Notificator;
use Modules\Seller\Entities\AccountModel as Account;
use Modules\Seller\Entities\RekeningBankModel as ListBank;
use Modules\User\Entities\CartModel as Carts;
use Modules\Seller\Entities\ProductModel as Product;
use Modules\User\Entities\OrderModel as Orders;
use Modules\User\Entities\OrderHistoryModel as Detail;
use Modules\Seller\Entities\BillingModel as Billing;
use App\Models\LogActivites;

class AccountController extends Controller
{

    public $totalOrders; // total order of user

    public function index()
    {
        $data = [
            'subtitle' => 'Akun'
        ];

        return view('user::account.index', compact('data'));
    }
    public function updateProfile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'password' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        $findUser = Account::find($id);

        if($findUser) {
            // update data
            $findUser->name = $input['name'];
            $findUser->email = $input['email'];
            $findUser->phone = $input['phone'];
            if(empty($input['password'])) {
                $findUser->password = $findUser->password;
            } else {
                $findUser->password = bcrypt($input['password']);
            }

            // save data
            $findUser->save();
            return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil disimpan'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan data!'));
        }
    }
    public function cart()
    {
        $data = [
            'subtitle' => 'Keranjang'
        ];

        $getListCart = Carts::session(user()->id)->getCart();
        return view('user::transaction.cart', compact('data', 'getListCart'));
    }

    public function checkout(Request $request)
    {
        $input = $request->all();
        if(empty($input['cartItem'])) {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan produk yang dipilih!'));
        }

        $data = [
            'subtitle' => 'Checkout'
        ];

        $checkoutItem = array_map(function($item) {
            $getInfoCart = Carts::find($item);

            $name = $getInfoCart->name;
            $categoryItem = null;
            $items = [];

            $detailItems = Product::where('name', 'like', '%' . $name . '%')->first();
            $items = [
                'item_id' => $item,
                'user_id' => $detailItems->user_id,
                'price' => (int) $detailItems->is_price,
                'name' => $detailItems->name,
                'description' => $detailItems->description,
                'type' => $detailItems->is_type == 1 ? 'Single' : 'Bundling',
            ];
            $this->totalOrders += (int) $detailItems->is_price;

            return [
                'id' => $item,
                'name' => $name,
                'items' => $items,
            ];

        }, $input['cartItem']);

        $totalCart = $this->totalOrders;
        $showPlaceOrder = true;

        return view('user::transaction.checkout', compact('data', 'checkoutItem', 'totalCart', 'showPlaceOrder'));
    }

    public function deleteCart($id)
    {
        $cart = Carts::find($id)->delete();
        if($cart) {
            return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil dihapus'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan od sistem!'));
        }
    }

    public function placeOrder(Request $request)
    {
        $input = $request->all();
        $listCheckout = [];
        $totalTagihan = 0;

        $invoiceNumber = invoiceGenerator();

        foreach($input['content'] as $key => $itemId) {
            $keyExist = explode(',', $key);
            // find item
            $itemBox = $keyExist[0];
            $itemName = $keyExist[1];

            // find seller id
            $selectCarts = Carts::where('id', $itemName)->first();
            $selectUrlsFromCarts = explode(' ', $selectCarts->name)[1];
            $totalAmount = $selectCarts->price;

            $findSellerFromUrls = Product::where('name', 'like', '%' . $selectUrlsFromCarts . '%');
            if($findSellerFromUrls->count() > 0) {
                $detailSellerWebsite = $findSellerFromUrls->first();
            }
            $totalTagihan += $totalAmount;
            $listWebsite[] = $selectUrlsFromCarts;

            // additional automatically added
            $totalPrice = ($totalAmount);
            $content = $input['content'][$key][0];
            $tanggal = $input['tanggal'][$key][0];

            $listCheckout[] = [
                'id' => Str::uuid()->toString(),
                'buy_id' => user()->id,
                'seller_id' => $detailSellerWebsite->user_id,
                'order_id' => $invoiceNumber,
                'product_id' => $detailSellerWebsite->id,
                'price' => $totalAmount,
                'is_status' => 1,
                'comment' => $content,
                'tanggal' => $tanggal
            ];
        }

        $initPayment = virtual_gateway([
            'amount' => $totalTagihan,
            'invoice' => $invoiceNumber,
            'method' => 'VC'
        ]);

        $check = Orders::where('invoice_number', $invoiceNumber)->count();
        if($check == 0) {
            if($initPayment['status'] === 'success') {
                $url_payment =  $initPayment['url'];
            } else {
                $url_payment = NULL;
            }

            $newOrder = new Orders([
                'id' => Str::uuid(),
                'user_id' => user()->id,
                'invoice_number' => $invoiceNumber,
                'price' => $totalTagihan,
                'url_payment' => $url_payment,
                'is_status' => 1
            ]);

            if($newOrder->save()) {
                $insertDetail = Detail::insert($listCheckout);
                if($insertDetail) {
                    LogActivites::default([
                        'causedBy' => user()->id,
                        'logType' => GlobalEnum::LogOfGeneral,
                        'withContent' => [
                            'status' => 'add',
                            'text' => 'Anda berhasil melakukan transaksi baru pada tanggal ' . date('Y-m-d H:i:s'),
                        ]
                    ]);
                    if($url_payment != NULL) {
                        return redirect()->to($url_payment);
                    } else {
                        return redirect()->to(site_url('user', 'account/cart'))->with('swal', swal_alert('error', 'Gagal melakukan transaksi [500].'));
                    }
                } else {
                    return redirect()->to(site_url('user', 'account/cart'))->with('swal', swal_alert('error', 'Gagal melakukan transaksi [600].'));
                }
            } else {
                return redirect()->to(site_url('user', 'account/cart'))->with('swal', swal_alert('error', 'Gagal melakukan transaksi [700].'));
            }
        }

        // dd($listCheckout);
    }

    public function activity()
    {
        $logsActivity = LogActivites::where('logType', GlobalEnum::LogOfLogin)->where('causedBy', user()->id)->orderBy('created_at', 'desc')->limit(5)->get();
        $logGeneral = LogActivites::where('logType', GlobalEnum::LogOfGeneral)->where('causedBy', user()->id)->orderBy('created_at', 'desc')->limit(5)->get();

        $data = [
            'subtitle' => 'Riwayat Aktifitas'
        ];
        return view('user::account.activity', compact('data', 'logsActivity', 'logGeneral'));
    }
}
