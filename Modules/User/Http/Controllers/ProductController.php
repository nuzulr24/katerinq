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

use Modules\Seller\Entities\ProductModel;
use Modules\User\Entities\CartModel as Carts;
use Modules\User\Entities\OrderModel as Order;
use App\Enums\GlobalEnum as Status;

class ProductController extends Controller
{
    public function index()
    {
        $data = [
            'subtitle' => 'Produk',
        ];

        return view('user::product.index', compact('data'));
    }

    public function view($id)
    {
        $sites = ProductModel::find($id);

        if(empty($sites)){
            return redirect()->back();
        }

        $data = [
            'subtitle' => $sites->name,
        ];

        $listCart = Carts::session(user()->id)->getCart();
        return view('user::product.detail', compact('data', 'sites', 'listCart'));
    }

    public function addToCart($id)
    {
        $sites = ProductModel::find($id);
        if(empty($sites)){
            return redirect()->back();
        }

        $name = $sites->name;
        $price = $sites->is_price;

        $check = Carts::where('user_id', user()->id)->where('name', 'LIKE', '%' . $name . '%')->first();

        if(!empty($check)){
            return redirect()->back()->with('swal', swal_alert('error', 'Produk sudah ada di keranjang.'));
        }

        Carts::create([
            'user_id' => user()->id,
            'name' => $name,
            'price' => $price,
            'quantity' => 1,
            'id' => Str::random(5),
        ]);

        return redirect()->route('user.account.cart')->with('swal', swal_alert('success', 'Berhasil menambahkan ke keranjang.'));
    }
}
