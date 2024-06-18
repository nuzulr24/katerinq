<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;

use App\Enums\GlobalEnum;
use Carbon\Carbon;
use App\Models\Website;

use Modules\User\Entities\OrderModel as Order;
use Modules\Seller\Entities\WithdrawalModel as Withdrawal;
use App\Models\LogActivites;

class MoreController extends Controller
{

    public function index()
    {
        $data = [
            'subtitle' => 'Pengaturan',
        ];

        return view('admin.app.more.index', compact('data'));
    }

    public function media()
    {
        $data = [
            'subtitle' => 'Media',
        ];

        return view('admin.app.more.media', compact('data'));
    }

    public function seo()
    {
        $data = [
            'subtitle' => 'Search Engine Optimization',
        ];

        return view('admin.app.more.seo', compact('data'));
    }

    public function surel()
    {
        $data = [
            'subtitle' => 'Surel / SMTP',
        ];

        return view('admin.app.more.surel', compact('data'));
    }

    public function payment()
    {
        $data = [
            'subtitle' => 'API Pembayaran',
        ];

        return view('admin.app.more.payment', compact('data'));
    }

    public function packages()
    {
        $data = [
            'subtitle' => 'Layanan Paket',
        ];

        return view('admin.app.more.packages', compact('data'));
    }

    public function advertiser()
    {
        $data = [
            'subtitle' => 'Layanan Paket Advertiser',
        ];

        return view('admin.app.more.advertiser', compact('data'));
    }

    public function fees()
    {
        $data = [
            'subtitle' => 'Biaya Pajak',
        ];

        return view('admin.app.more.fees', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'short_info' => 'string',
            'about' => 'string',
            'address' => 'string',
            'email' => 'string|email',
            'phone' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', $validator->errors()->first()))->withInput();
        }

        $input = $request->all();
        $findWebsiteTitle = Website::find(1);
        $findWebsiteShort = Website::find(6);
        $findWebsiteAbout = Website::find(15);
        $findWebsiteAddress = Website::find(9);
        $findWebsiteEmail = Website::find(11);
        $findWebsitePhone = Website::find(10);

        // update data
        $findWebsiteTitle->value = $input['title'];
        $findWebsiteShort->value = $input['short_info'];
        $findWebsiteAbout->value = clean($input['about']);
        $findWebsiteAddress->value = $input['address'];
        $findWebsiteEmail->value = $input['email'];
        $findWebsitePhone->value = $input['phone'];

        // save data
        $findWebsiteTitle->save();
        $findWebsiteShort->save();
        $findWebsiteAbout->save();
        $findWebsiteAddress->save();
        $findWebsiteEmail->save();
        $findWebsitePhone->save();

        return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil disimpan'));
    }

    public function storeSeo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meta_title' => 'required',
            'meta_keywords' => 'required',
            'meta_description' => 'required',
            'gtag_manager' => 'string',
            'fb_pixel' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', $validator->errors()->first()))->withInput();
        }

        $input = $request->all();
        $findMetaTitle = Website::find(5);
        $findMetaKeywords = Website::find(4);
        $findMetaDescription = Website::find(3);
        $findGoogleTag = Website::find(34);
        $findFacebookPixel = Website::find(35);

        // update data
        $findMetaTitle->value = $input['meta_title'];
        $findMetaKeywords->value = $input['meta_keywords'];
        $findMetaDescription->value = $input['meta_description'];
        $findGoogleTag->value = $input['gtag_manager'];
        $findFacebookPixel->value = $input['fb_pixel'];

        // save data
        $findMetaTitle->save();
        $findMetaKeywords->save();
        $findMetaDescription->save();
        $findGoogleTag->save();
        $findFacebookPixel->save();

        return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil disimpan'));
    }

    public function storePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'duitku_merchant' => 'required',
            'duitku_client' => 'required',
            'duitku_sandbox' => 'required',
            'payment_duitku' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', $validator->errors()->first()))->withInput();
        }

        $input = $request->all();
        $findMerchant = Website::find(36);
        $findClient = Website::find(37);
        $findProduct = Website::find(38);
        $findActive = Website::find(39);

        // update data
        $findMerchant->value = $input['duitku_merchant'];
        $findClient->value = $input['duitku_client'];
        $findProduct->value = $input['duitku_sandbox'];
        $findActive->value = empty($input['payment_duitku']) ? 0 : 1;

        // save data
        $findMerchant->save();
        $findClient->save();
        $findProduct->save();
        $findActive->save();

        return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil disimpan'));
    }

    public function storeFees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform_percentage' => 'required',
            'deposit_percentage' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', $validator->errors()->first()))->withInput();
        }

        $input = $request->all();
        $findDeposit = Website::find(26);
        $findPlatform = Website::find(25);

        // update data
        $findDeposit->value = $input['deposit_percentage'];
        $findPlatform->value = $input['platform_percentage'];

        // save data
        $findDeposit->save();
        $findPlatform->save();

        return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil disimpan'));
    }

    public function storeSurel(Request $request)
    {

    }

    public function storeMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,svg|max:7048',
        ], [
            'image.mimes' => 'Tipe file yang diunggah harus jpg, jpeg, png, atau svg.',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withInput()->with('swal', swal_alert('error', 'Unexpected error, please try again. code: ' . $validator->errors()->first()));
        }

        $input = $request->all();
        if($input['type'] === "logo") {
            $findMedia = Website::find(23);
        } else {
            $findMedia = Website::find(2);
        }

        if($findMedia) {
            if($input['type'] === "logo") {
                // Jika ada file baru yang diunggah, simpan file baru di storage
                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    $foto_namaBaru = $request->file('image')->store('public/images');
                    $findMedia->value = $foto_namaBaru;

                    $findMedia->save();
                    return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil disimpan'));
                }
            } else {
                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    $foto_namaBaru = $request->file('image')->store('public/images');
                    $findMedia->value = $foto_namaBaru;

                    $findMedia->save();
                    return redirect()->back()->with('swal', swal_alert('success', 'Data berhasil disimpan'));
                }
            }
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Tidak ditemukan data'));
        }
    }

    public function cashflow()
    {
        $getTotalIncomeOrder = Order::sum('price');
        $getTotalPendingIncomeOrder = Order::where('is_status', GlobalEnum::isOrderOnWorking)->sum('price');
        $getListOrder = Order::orderBy('created_at', 'desc')->limit(5)->get();
        $getFlowOrder = Order::getTotalAmountByMonth();
        $getTotalOrder = Order::count();
        $getListAwaitingWithdrawal = Withdrawal::where('is_status', 1)->count();

        $data = [
            'subtitle' => 'Keuangan dan Transaksi'
        ];
        return view('admin.app.more.cashflow', compact('data', 'getTotalIncomeOrder', 'getTotalPendingIncomeOrder', 'getListOrder', 'getFlowOrder', 'getListAwaitingWithdrawal', 'getTotalOrder'));
    }

    public function statistics(Request $request)
    {
        $logsActivity = LogActivites::where('logType', GlobalEnum::LogOfLogin)->orderBy('created_at', 'desc')->limit(10)->get();
        $logGeneral = LogActivites::where('logType', GlobalEnum::LogOfGeneral)->orderBy('created_at', 'desc')->limit(10)->get();

        $data = [
            'subtitle' => 'Riwayat Aktifitas'
        ];
        return view('admin.app.more.statistic', compact('data', 'logsActivity', 'logGeneral'));
    }
}
