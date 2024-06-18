<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Roles;
use App\Models\Permission;

use Modules\Seller\Entities\SitesModel;
use Modules\Seller\Entities\AccountModel;
use App\Enums\GlobalEnum;
use App\Models\LogActivites;
use App\Models\Seller;

use DataTables;
use Carbon\Carbon;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*')->orderBy('created_at', 'desc');
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('title-post', function($row) {
                    return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-25px symbol-circle">
                            <div class="symbol-label" style="background-image:url(' . gravatar_team($row->email) . ')"></div>
                        </div>
                        <div class="ms-3"><span>' . $row->name . '</span></div>
                    </div>
                    ';
                })
                ->addColumn('role', function($row) {
                    if ($row->level == GlobalEnum::isAdmin) {
                        return '<span class="mb-1 badge font-medium badge-light-success py-3 px-4 fs-7">Admin</span>';
                    } elseif($row->level == GlobalEnum::isModerator) {
                        return '<span class="mb-1 badge font-medium badge-light-primary py-3 px-4 fs-7">Moderator</span>';
                    } elseif($row->level == 2) {
                        return '<span class="mb-1 badge font-medium badge-light-danger py-3 px-4 fs-7">Vendor</span>';
                    } else {
                        return '<span class="mb-1 badge font-medium badge-light-info py-3 px-4 fs-7">Konsumen</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $view = route('users.show', ['id' => $row->id]);
                    $edit = route('users.edit', ['id' => $row->id]);
                    $delete = route('users.delete', ['id' => $row->id]);
                    $btn = '
                    <a href="' . $view . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-eye"></i></a>
                    <a href="' . $edit . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-pencil"></i></a>
                    <a data-url="' . $delete . '" href="#" class="btn btn-light btn-sm deleteContent px-4"><i class="ki-outline ki-trash"></i></a>
                    ';
                    return $btn;
                })
                ->addColumn('status', function($row){
                    if ($row->status == GlobalEnum::isActive) {
                        return '<span class="mb-1 badge font-medium badge-light-success py-3 px-4 fs-7">Active</span>';
                    } elseif($row->status == GlobalEnum::isInactive) {
                        return '<span class="mb-1 badge font-medium badge-light-primary py-3 px-4 fs-7">Non Active</span>';
                    } elseif($row->status == GlobalEnum::isDeactive) {
                        return '<span class="mb-1 badge font-medium badge-light-danger py-3 px-4 fs-7">Deactivated</span>';
                    } else {
                        return '<span class="mb-1 badge font-medium badge-light-warning py-3 px-4 fs-7">Not Verified</span>';
                    }
                })
                ->rawColumns(['title-post','action','status','role'])
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
                        } elseif($filterCategory[0] === 'user') {
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
            'subtitle' => 'Users',
            'button' => true,
            'module' => [
                'url' => route('users.create'),
                'name' => 'Create New'
            ]
        ];

        return view('admin.app.users.index', compact('data'));
    }

    public function create()
    {
        $data = [
            'subtitle' => 'Create New'
        ];
        return view('admin.app.users.add', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'status' => 'required',
            'level' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,svg|max:7048',
        ], [
            'image.mimes' => 'Tipe file yang diunggah harus jpg, jpeg, png, atau svg.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        $foto_namaBaru = null;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $foto_namaBaru = $request->file('image')->store('public/images');
        }

        $post = new User([
            'name' => $input['name'],
            'username' => Str::before($input['email'], '@') . rand(100, 999),
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
            'status' => $input['status'],
            'level' => $input['level'],
            'thumbnail' => empty($foto_namaBaru) ? '' : $foto_namaBaru,
        ]);

        $check = User::where('email', $input['email'])->count();
        $insertLog = LogActivites::default([
            'causedBy' => user()->id,
            'logType' => GlobalEnum::LogOfGeneral,
            'withContent' => [
                'status' => 'add',
                'text' => 'Insert a new user with email ' . $input['email'],
            ]
        ]);
        if ($check == 0) {
            if ($post->save() && $insertLog) {
                return redirect()->route('users')->with('swal', swal_alert('success', 'You have successfully added data'));
            } else {
                return redirect()->route('users')->with('swal', swal_alert('error', 'An error occurred in the query'));
            }
        } else {
            return redirect()->route('users')->with('swal', swal_alert('error', 'Email already exists'));
        }
    }

    public function show($id)
    {
        $data = [
            'subtitle' => User::where('id', $id)->first()->email,
            'records' => User::where('id', $id)->first(),
            'logs' => User::where('id', $id)->first()
        ];
        return view('admin.app.users.detail', compact('data'));
    }

    public function edit($id)
    {
        $data = [
            'subtitle' => User::where('id', $id)->first()->email,
            'records' => User::where('id', $id)->first()
        ];
        return view('admin.app.users.edit', compact('data', 'id'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'level' => 'required',
            'status' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,svg|max:7048',
        ], [
            'image.mimes' => 'Tipe file yang diunggah harus jpg, jpeg, png, atau svg.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cari data berdasarkan ID
        $user = User::find($id);

        // Jika data ditemukan
        if ($user) {
            // Jika ada file baru yang diunggah, hapus file thumbnail yang lama
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($user->thumbnail) {
                    Storage::delete($user->thumbnail);
                }
            }

            // Update data dengan data baru dari form yang telah dibersihkan
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            !empty($request->input('password')) ? $user->password = bcrypt($request->input('password')) : $user->password;
            $user->level = $request->input('level');
            $user->status = $request->input('status');
            $user->username = Str::before($user->email, '@') . rand(100, 999);

            // Jika ada file baru yang diunggah, simpan file baru di storage
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $foto_namaBaru = $request->file('image')->store('public/images');
                $user->thumbnail = $foto_namaBaru;
            }

            // Simpan perubahan pada database
            $user->save();
            return redirect()->route('users')->with('swal', swal_alert('success', 'You are successfully modify data'));
        } else {
            return redirect()->route('users')->with('swal', swal_alert('error', 'Unexpected error'));
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        // Jika data ditemukan
        if ($user) {
            // Cek apakah ada file di kolom "is_thumbnail"
            if ($user->thumbnail) {
                // Hapus file thumbnail dari storage
                Storage::delete($user->is_thumbnail);
            }
            // Hapus data dari database
            $user->delete();
            return redirect()->route('users')->with('swal', swal_alert('success', 'You are successfully deleted records'));
        } else {
            return redirect()->route('users')->with('swal', swal_alert('error', 'Data not found'));
        }
    }

    /**
     * Retrieves the roles from the database and returns them as a DataTables response.
     *
     * @param Request $request The HTTP request object.
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value The DataTables response with roles data.
     */
    public function roles(Request $request)
    {
        if ($request->ajax()) {
            $data = Roles::select('*');
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $edit = route('roles.edit', ['id' => $row->id]);
                    $btn = '
                    <a href="' . $edit . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-pencil"></i></a>
                    ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Roles',
        ];

        return view('admin.app.users.roles.index', compact('data'));
    }

    public function editRoles($id)
    {
        $data = [
            'subtitle' => Roles::where('id', $id)->first()->name,
            'records' => Roles::where('id', $id)->first()
        ];
        return view('admin.app.users.roles.edit', compact('data', 'id'));
    }

    public function updateRoles(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cari data berdasarkan ID
        $user = Roles::find($id);

        // Jika data ditemukan
        if ($user) {
            // Update data dengan data baru dari form yang telah dibersihkan
            $user->name = $request->input('name');
            // Simpan perubahan pada database
            $user->save();
            return redirect()->route('roles')->with('swal', swal_alert('success', 'You are successfully modify data'));
        } else {
            return redirect()->route('roles')->with('swal', swal_alert('error', 'Unexpected error'));
        }
    }

    public function permission(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::whereNull('idParent');
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('updated_at', function($row) {
                    return $row->updated_at;
                })
                ->addColumn('action', function($row){
                    $edit = route('permission.edit', ['id' => $row->id]);
                    $delete = route('permission.delete', ['id' => $row->id]);
                    $detail = route('permission.detail', ['slug' => $row->slug]);
                    $btn = '
                    <a href="' . $detail . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-eye"></i></a>
                    <a href="' . $edit . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-pencil"></i></a>
                    <a href="' . $delete . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-trash"></i></a>
                    ';
                    return $btn;
                })
                ->rawColumns(['action','updated_at'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Permission',
            'button' => true,
            'module' => [
                'url' => route('permission.create'),
                'name' => 'Create New'
            ]
        ];

        return view('admin.app.users.permission.index', compact('data'));
    }

    public function createPermission()
    {
        $data = [
            'subtitle' => 'Create Permission',
        ];
        $routeInApplication = routesAll();

        return view('admin.app.users.permission.add', compact('data','routeInApplication'));
    }

    public function editPermission($id)
    {
        $data = [
            'subtitle' => Permission::where('id', $id)->first()->name,
            'records' => Permission::where('id', $id)->first()
        ];
        return view('admin.app.users.permission.edit', compact('data'));
    }

    public function storePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', 'Validation required'))->withInput();
        }
        $input = $request->all();

        $permission = new Permission([
            'slug' => Str::random(10),
            'name' => $input['name']
        ]);

        $check = Permission::where('name', $input['name'])->count();
        if ($check == 0) {
            if ($permission->save()) {
                return redirect()->route('permission')->with('swal', swal_alert('success', 'You have successfully added data'));
            } else {
                return redirect()->route('permission')->with('swal', swal_alert('error', 'An error occurred in the query'));
            }
        } else {
            return redirect()->route('permission')->with('swal', swal_alert('error', 'Guard Name already exists'));
        }
    }

    public function updatePermission(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
             return redirect()->back()->with('swal', swal_alert('error', 'Validation required'))->withInput();
        }

        // Cari data berdasarkan ID
        $permission = Permission::find($id);

        // Jika data ditemukan
        if ($permission) {
            $permission->name = $request->input('name');

            // Simpan perubahan pada database
            $permission->save();
            return redirect()->route('permission')->with('swal', swal_alert('success', 'You are successfully modify data'));
        } else {
            return redirect()->route('permission')->with('swal', swal_alert('error', 'Unexpected error'));
        }
    }

    public function deletePermission($id)
    {
        $permission = Permission::find($id);
        // Jika data ditemukan
        if ($permission) {
            $permission->delete();
            return redirect()->route('permission')->with('swal', swal_alert('success', 'You are successfully deleted records'));
        } else {
            return redirect()->route('permission')->with('swal', swal_alert('error', 'Data not found'));
        }
    }

    public function detailPermission(Request $request, $slug)
    {
        if ($request->ajax()) {
            $getIdPermissionBySlug = Permission::where('slug', $slug)->first();
            $data = Permission::select("*")->where("idParent", "=", $getIdPermissionBySlug->id)->get();
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('nameOfPermission', function($row) {
                    $var = '
                        <p class="text-primary mb-0">Name: <span class="text-muted">' . $row->name . '</span></p>
                        <p class="text-primary mb-0">Rule: <span class="text-muted">' . $row->guard_name . '</span></p>
                        <p class="text-primary mb-0">Last Updated: <span class="text-muted">' . $row->updated_at . '</span></p>
                    ';

                    return $var;
                })
                ->addColumn('updated_at', function($row) {
                    return $row->updated_at;
                })
                ->addColumn('action', function($row){
                    $edit = route('permission.edit.child', ['id' => $row->id]);
                    $delete = route('permission.delete.child', ['id' => $row->id]);
                    $btn = '
                    <a href="' . $edit . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-pencil"></i></a>
                    <a href="' . $delete . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-trash"></i></a>
                    ';
                    return $btn;
                })
                ->rawColumns(['nameOfPermission','action','updated_at'])
                ->make(true);
        }

        $data = [
            'subtitle' => Permission::where('slug', $slug)->first()->name,
            'records' => Permission::where('slug', $slug)->first()
        ];
        $routeInApplication = routesAll();

        return view('admin.app.users.permission.detail', compact('data', 'slug','routeInApplication'));
    }

    public function storeChildPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'guard_name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        $getPermissionDetail = Permission::where('id', $input['idParent'])->first();
        $permission = new Permission([
            'slug' => Str::random(6),
            'idParent' => $getPermissionDetail->id,
            'name' => $input['name'],
            'guard_name' => $input['guard_name'],
        ]);

        $check = Permission::where('name', $input['name'])->count();
        if ($check == 0) {
            if ($permission->save()) {
                return redirect()->route('permission.detail', ['slug' => $getPermissionDetail->slug])->with('swal', swal_alert('success', 'You have successfully added data'));
            } else {
                return redirect()->route('permission.detail', ['slug' => $getPermissionDetail->slug])->with('swal', swal_alert('error', 'An error occurred in the query'));
            }
        }
    }

    public function editChildPermission($id)
    {
        $data = [
            'subtitle' => Permission::where('id', $id)->first()->name,
            'records' => Permission::where('id', $id)->first()
        ];
        return view('admin.app.users.permission.edit.child', compact('data', 'id'));
    }

    public function updateChildPermission(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'guard_name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cari data berdasarkan ID
        $permission = Permission::find($id);
        $getPermissionDetail = Permission::where('id', $permission->idParent)->first();

        // Jika data ditemukan
        if ($permission) {
            $permission->name = $request->input('name');
            $permission->guard_name = $request->input('guard_name');

            // Simpan perubahan pada database
            $permission->save();
            return redirect()->route('permission.detail', ['slug' => $getPermissionDetail->slug])->with('swal', swal_alert('success', 'You are successfully modify data'));
        } else {
            return redirect()->route('permission.detail', ['slug' => $getPermissionDetail->slug])->with('swal', swal_alert('error', 'Unexpected error'));
        }
    }

    public function deleteChildPermission($id)
    {
        $permission = Permission::find($id);
        $getPermissionDetail = Permission::where('id', $permission->idParent)->first();
        $permission->delete();
        return redirect()->route('permission.detail', ['slug' => $getPermissionDetail->slug])->with('swal', swal_alert('success', 'You are successfully deleted records'));
    }

    // seller
    public function sellers(Request $request)
    {
        if ($request->ajax()) {
            $data = Seller::select('*');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('title-post', function($row) {
                    $user = User::where('id', $row->user_id)->first();
                    return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-25px symbol-circle">
                            <div class="symbol-label" style="background-image:url(' . gravatar_team($user->email) . ')" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="' . $user->name . '" aria-label="' . $user->name . '"></div>
                        </div>
                        <div class="ms-3"><span>' . $row->name . '</span></div>
                    </div>
                    ';
                })
                ->addColumn('status', function($row){
                    if ($row->is_active == GlobalEnum::isSellerActive) {
                        return '<span class="mb-1 badge font-medium badge-light-primary py-3 px-4 fs-7">Aktif</span>';
                    } else {
                        return '<span class="mb-1 badge font-medium badge-light-danger py-3 px-4 fs-7">Tidak Aktif</span>';
                    }
                })
                ->rawColumns(['title-post','status'])
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $search = $request->get('search')['value'];

                        $filterCategory = explode('|', $search);
                        if($filterCategory[0] === 'status') {
                            if(!empty($filterCategory[1])) {
                                $query->where('is_active', '=', $filterCategory[1]);
                            } else {
                                $query->get();
                            }
                        } elseif($filterCategory[0] === 'user') {
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
            'subtitle' => 'Seller',
            'button' => true,
            'module' => [
                'url' => route('users.sellers.create'),
                'name' => 'Create New'
            ]
        ];

        return view('admin.app.users.sellers.index', compact('data'));
    }

    public function createSeller()
    {
        $data = [
            'subtitle' => 'Tambah Baru'
        ];

        $getUserLists = User::where('status', GlobalEnum::isSellerActive)->get();
        return view('admin.app.users.sellers.add', compact('data', 'getUserLists'));
    }

    public function storeSeller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'alias' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();

        $post = new Seller([
            'uuid' => Str::uuid(),
            'name' => $input['name'],
            'alias' => $input['alias'],
            'is_active' => 1,
            'user_id' => $input['user_id']
        ]);

        $check = Seller::where('name', $input['name'])->count();
        $insertLog = LogActivites::default([
            'causedBy' => user()->id,
            'withContent' => [
                'status' => 'add',
                'text' => 'Insert a new seller with name ' . $input['name'],
            ]
        ]);
        if ($check == 0) {
            if ($post->save() && $insertLog) {
                return redirect()->route('users.sellers')->with('swal', swal_alert('success', 'You have successfully added data'));
            } else {
                return redirect()->route('users.sellers')->with('swal', swal_alert('error', 'An error occurred in the query'));
            }
        } else {
            return redirect()->route('users.sellers')->with('swal', swal_alert('error', 'Name already exists'));
        }
    }

    public function showSeller(Request $request, $id)
    {
        if ($request->ajax()) {
            $getUserId = Seller::where('uuid', $id)->first()->user_id;
            $data = SitesModel::where('user_id', $getUserId)->select('url','user_id','is_language','created_at','is_url_category','id','is_status','is_post_price','is_delivery_time','is_word_limit');
            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('title-post', function($row){
                    $text = '
                    <p class="mb-0">' . removeUrlPrefix($row->url) . '</p>
                    ';
                    return $text;
                })
                ->addColumn('author', function($row){
                    $user = AccountModel::where('id', $row->user_id)->first();
                    return '
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-25px symbol-circle">
                            <div class="symbol-label" style="background-image:url(' . gravatar_team(!empty($user->email) ? $user->email : 'random' . rand(111, 999) . '@gmail.com') . ')"></div>
                        </div>
                        <div class="ms-3"><span>' . $user->name . '</span></div>
                    </div>
                    ';
                })
                ->addColumn('action', function($row){
                    $view = route('site.detail', ['id' => $row->id]);
                    $edit = route('site.edit', ['id' => $row->id]);
                    $delete = route('site.delete', ['id' => $row->id]);
                    $btn = '
                    <a href="#" data-id="' . $row->id . '" class="btn btn-light btn-sm px-4 viewContent"><i class="ki-outline ki-eye"></i></a>
                    <a href="' . $edit . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-pencil"></i></a>
                    <a data-url="' . $delete . '" href="#" class="btn btn-light btn-sm deleteContent px-4"><i class="ki-outline ki-trash"></i></a>
                    ';
                    return $btn;
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
                ->addColumn('language', function($row){
                    if (preg_match('/^indonesia/i', $row->is_language)) {
                        return '
                        <span class="fs-8 rounded bg-light py-3 px-2">
                            <img class="w-15px h-15px rounded-1 mx-1" src="' . frontend('media/flags/indonesia.svg') . '" alt="">
                            Indonesia
                        </span>
                        ';
                    } else {
                        return '
                        <span class="fs-8 rounded bg-light py-3 px-2">
                            <img class="w-15px h-15px rounded-1 mx-1" src="' . frontend('media/flags/united-states.svg') . '" alt="">
                            English
                        </span>
                        ';
                    }
                })
                ->addColumn('post_price', function($row){
                    return 'Rp. ' . number_format($row->is_post_price, 0, ',', '.');
                })
                ->addColumn('delivery_time', function($row){
                    return $row->is_delivery_time . ' Hari';
                })
                ->addColumn('word_limit', function($row){
                    return $row->is_word_limit . ' Kata';
                })
                ->rawColumns(['title-post','author','language','action','is_status','post_price','delivery_time','word_limit'])
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
                                $query->where('url', 'LIKE', "%$filterCategory[1]%");
                            } else {
                                $query->get();
                            }
                        }
                    }
                })
                ->make(true);
        }
        $data = [
            'subtitle' => Seller::where('uuid', $id)->first()->name,
        ];

        $getSellerInfo = Seller::where('uuid', $id)->first();
        $getSiteLists = SitesModel::where('user_id', $getSellerInfo->user_id)->get();

        return view('admin.app.users.sellers.detail', compact('data', 'id', 'getSiteLists', 'getSellerInfo'));
    }

    public function editSeller($id)
    {
        $data = [
            'subtitle' => 'Seller: ' . Seller::where('uuid', $id)->first()->name,
            'records' => Seller::where('uuid', $id)->first()
        ];

        $getUserLists = User::where('status', GlobalEnum::isSellerActive)->get();
        return view('admin.app.users.sellers.edit', compact('data', 'id', 'getUserLists'));
    }

    public function updateSeller(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'alias' => 'required',
            'user_id' => 'required',
            'is_active' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cari data berdasarkan ID
        $user = Seller::find($id);

        // Jika data ditemukan
        if ($user) {
            // Update data dengan data baru dari form yang telah dibersihkan
            $user->user_id = $request->input('user_id');
            $user->name = $request->input('name');
            $user->alias = $request->input('alias');
            $user->is_active = $request->input('is_active');
            $user->save();
            return redirect()->route('users.sellers')->with('swal', swal_alert('success', 'You are successfully modify data'));
        } else {
            return redirect()->route('users.sellers')->with('swal', swal_alert('error', 'Unexpected error'));
        }
    }

    public function destroySeller($id)
    {
        $user = Seller::find($id);
        // Jika data ditemukan
        if ($user) {
            // Hapus data dari database
            $user->delete();
            return redirect()->route('users.sellers')->with('swal', swal_alert('success', 'You are successfully deleted records'));
        } else {
            return redirect()->route('users.sellers')->with('swal', swal_alert('error', 'Data not found'));
        }
    }

}
