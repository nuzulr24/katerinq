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

use Modules\Seller\Entities\ProductModel;
use App\Models\Seller;
use App\Enums\GlobalEnum;
use App\Models\User;

use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = ProductModel::orderBy('created_at', 'DESC');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('title-post', function($row){
                    return $row->name;
                })
                ->addColumn('author', function($row){
                    $user = User::where('id', $row->user_id)->first();
                    if($user) {
                        $name = $user->name;
                        $email = gravatar_team($user->email);
                    } else {
                        $name = '-';
                        $email = gravatar_team('random' . rand(111, 999) . '@gmail.com');
                    }
                    return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-25px symbol-circle">
                            <div class="symbol-label" style="background-image:url(' . $email . ')"></div>
                        </div>
                        <div class="ms-3"><span>' . $name . '</span></div>
                    </div>
                    ';
                })
                ->addColumn('is_status', function($row){
                    if ($row->is_status == GlobalEnum::isSiteActive) {
                        return '<span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7">Aktif</span>';
                    } elseif($row->is_status == GlobalEnum::isSiteInReview) {
                        return '<span class="mb-1 badge font-medium bg-light-primary text-primary py-3 px-4 fs-7">Dalam review</span>';
                    } elseif($row->is_status == GlobalEnum::isSiteNotActive) {
                        return '<span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7">Tidak aktif</span>';
                    } elseif($row->is_status == GlobalEnum::isSiteRejected) {
                        return '<span class="mb-1 badge font-medium bg-light-warning text-warning py-3 px-4 fs-7">Ditolak</span>';
                    } elseif($row->is_status == GlobalEnum::isSiteDeactivated) {
                        return '<span class="mb-1 badge font-medium bg-light-info text-info py-3 px-4 fs-7">Dimatikan</span>';
                    }
                })
                ->addColumn('type', function($row){
                    if ($row->is_type == 1) {
                        return 'Pre/Weeding';
                    } elseif($row->is_type == 2) {
                        return 'Engagement';
                    } elseif($row->is_type == 3) {
                        return 'Party';
                    } else {
                        return 'Other';
                    }
                })
                ->addColumn('price', function($row){
                    return 'Rp. ' . number_format($row->is_price, 0, ',', '.');
                })
                ->addColumn('delivery_time', function($row){
                    return $row->is_delivery_time . ' Hari';
                })
                ->rawColumns(['title-post', 'author', 'is_status', 'type', 'price', 'delivery_time'])
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
                        } elseif($filterCategory[0] === 'sites') {
                            if(!empty($filterCategory[1])) {
                                $query->where('name', 'LIKE', "%$filterCategory[1]%");
                            } else {
                                $query->get();
                            }
                        }
                    }
                })
                ->make(true);
        }

        $data = [
            'subtitle' => 'Semua Product',
            // 'button' => true,
            // 'module' => [
            //     'url' => app_url('product/create'),
            //     'name' => 'Tambah baru'
            // ]
        ];

        return view('admin.app.product.index', compact('data'));
    }
    public function create()
    {
        $data = [
            'subtitle' => 'Tambah Baru'
        ];
        return view('admin.app.product.add', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'type' => 'required',
            'is_price' => 'required',
            'is_delivery_time' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', $validator->errors()->first()))->withInput();
        }

        $input = $request->all();

        $post = new ProductModel([
            'id' => Str::uuid(),
            'user_id' => user()->id,
            'name' => $input['name'], // Membersihkan input judul menggunakan Purifier
            'description' => clean($input['description']), // Membersihkan input deskripsi menggunakan Purifier
            'is_role' => 1,
            'is_type' => $input['type'],
            'is_price' => $input['is_price'],
            'is_delivery_time' => $input['is_delivery_time'],
            'is_status' => 1,
        ]);

        $check = ProductModel::where('name', $input['name'])->where('is_type', $input['type'])->count();
        if ($check == 0) {
            if ($post->save()) {
                return redirect()->to(app_url('product'))->with('swal', swal_alert('success', 'Anda berhasil menambahkan data baru'));
            } else {
                return redirect()->back()->with('swal', swal_alert('error', 'Terjadi kesalahan pada sistem!'));
            }
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Galat! Ditemukan data yang sudah ada.'));
        }
    }

    public function edit($id)
    {
        $data = [
            'subtitle' => 'Edit',
        ];

        $getSitesInfo = ProductModel::find($id);
        return view('admin.app.product.edit', compact('data', 'getSitesInfo'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'type' => 'required',
            'is_price' => 'required',
            'is_delivery_time' => 'required',
            'is_status' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', $validator->errors()->first()))->withInput();
        }

        $input = $request->all();
        $sites = ProductModel::find($id);

        if($sites) {
            $sites->name = $input['name'];
            $sites->description = clean($input['description']);
            $sites->is_type = $input['type'];
            $sites->is_price = $input['is_price'];
            $sites->is_delivery_time = $input['is_delivery_time'];
            $sites->is_status = $input['is_status'];
            $sites->save();

            return redirect()->to(app_url('product'))->with('swal', swal_alert('success', 'Berhasil memperbarui data.'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Data tidak ditemukan.'));
        }
    }

    public function destroy($id)
    {
        $sites = ProductModel::find($id);
        if($sites) {
            $sites->delete();
            return redirect()->to(app_url('product'))->with('swal', swal_alert('success', 'Berhasil menghapus data.'));
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Data tidak ditemukan.'));
        }
    }

    public function detail($id)
    {
        $check = ProductModel::where('id', $id)->first();

        if($check) {
            $detail = [
                'name' => $check->name,
                'description' => $check->description,
                'is_role' => $check->is_role == GlobalEnum::isSiteOwner ? 'Owner' : 'Member',
                'is_type' => $check->is_type == 1 ? 'Pre/Weeding' : ($check->is_type == 2 ? 'Engagement' : ($check->is_type == 3 ? 'Party' : 'Other')),
                'is_price' => 'Rp. ' . number_format($check->is_price, '0', ',', '.'),
                'is_delivery_time' => $check->is_delivery_time,
                'is_seller' => $check->is_role == 1 ? 'Admin' :
                 (empty(Seller::where('user_id', $check->user_id)->first()) ? '-'
                 : Seller::where('user_id', $check->user_id)->first()->name),
            ];

            return $detail;
        }
    }
}
