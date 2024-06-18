<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Pages;
use Carbon\Carbon;
use DataTables;

class PagesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pages::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('title-post', function($row){
                    $text = '
                    <p class="mb-0">' . $row->title . '</p>
                    <p class="mb-0 small text-muted">Terakhir diperbarui pada ' . date_formatting(Carbon::parse($row->updated_at, 'Y-m-d'), 'timeago') . '</p>
                    ';
                    return $text;
                })
                ->addColumn('status', function($row){
                    if ($row->is_status == 1) {
                        return '<span class="mb-1 badge font-medium bg-light-primary text-primary py-3 px-4 fs-7">Publish</span>';
                    } else {
                        return '<span class="mb-1 badge font-medium bg-light text-dark py-3 px-4 fs-7">Draft</span>';
                    }
                })
                ->addColumn('unique', function($row){
                    if ($row->markAsUnique == 1) {
                        return '<i class="ki-outline ki-star text-warning fs-6"></i>';
                    } else {
                        return '<i class="ki-outline ki-cross text-danger fs-7"></i>';
                    }
                })
                ->rawColumns(['title-post','status','unique'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Laman',
            'button' => true,
            'module' => [
                'url' => route('pages.create'),
                'name' => 'Create New'
            ]
        ];

        return view('admin.app.pages.index', compact('data'));
    }

    public function create()
    {
        $data = [
            'subtitle' => 'Tambah baru',
        ];

        return view('admin.app.pages.add', compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'is_status' => 'required',
            'markAsUnique' => 'string',
            'is_created' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,svg|max:7048',
        ], [
            'image.mimes' => 'Tipe file yang diunggah harus jpg, jpeg, png, atau svg.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('swal', swal_alert('error', 'Unexpected error, please try again. code: ' . $validator->errors()->first(), []))->withInput();
        }

        $input = $request->all();
        $titleSlug = Str::slug($input['title']);
        $foto_namaBaru = null;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $foto_namaBaru = $request->file('image')->store('public/images');
        }

        $post = new Pages([
            'slug' => $titleSlug,
            'title' => $input['title'], // Membersihkan input judul menggunakan Purifier
            'description' => $input['description'], // Membersihkan input deskripsi menggunakan Purifier
            'is_status' => $input['is_status'],
            'is_created' => $input['is_created'], // Membersihkan input tanggal menggunakan Purifier
            'markAsUnique' => empty($input['markAsUnique']) ? 0 : 1,
            'is_thumbnail' => $foto_namaBaru,
        ]);

        $check = Pages::where('title', $input['title'])->count();
        if ($check == 0) {
            if ($post->save()) {
                return redirect()->route('pages')->with('swal', swal_alert('success', 'Anda berhasil menyimpan data'));
            } else {
                return redirect()->route('pages')->with('swal', swal_alert('error', 'Terjadi kesalahan saat menyimpan data'));
            }
        } else {
            return redirect()->route('pages')->with('swal', swal_alert('error', 'Data sudah ada'));
        }
    }

    public function show($id)
    {
        $data = [
            'subtitle' => Pages::where('slug', $id)->first()->title,
            'records' => Pages::where('slug', $id)->first()
        ];
        return view('admin.app.pages.detail', compact('data'));
    }

    public function edit($id)
    {
        $data = [
            'subtitle' => 'Edit: ' . Pages::where('id', $id)->first()->title,
            'records' => Pages::where('id', $id)->first(),
        ];
        $posts = Pages::FindOrFail($id);

        return view('admin.app.pages.edit', compact('data','posts'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input sebelum memperbarui data
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'is_status' => 'required',
            'markAsUnique' => 'string',
            'is_created' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,svg|max:7048',
        ], [
            'image.mimes' => 'Tipe file yang diunggah harus jpg, jpeg, png, atau svg.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('swal', swal_alert('error', 'Unexpected error, please try again. code: ' . $validator->errors()->first()));
        }

        // Cari data berdasarkan ID
        $post = Pages::find($id);

        // Jika data ditemukan
        if ($post) {
            // Jika ada file baru yang diunggah, hapus file thumbnail yang lama
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if ($post->is_thumbnail) {
                    Storage::delete($post->is_thumbnail);
                }
            }

            // Update data dengan data baru dari form yang telah dibersihkan
            $post->title = $request->input('title');
            $post->slug = Str::slug($request->input('title'));;
            $post->description = $request->input('description');
            $post->is_status = $request->input('is_status');
            $post->markAsUnique = empty($request->input('markAsUnique')) ? 0 : 1;
            $post->created_at = $request->input('is_created');

            // Jika ada file baru yang diunggah, simpan file baru di storage
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $foto_namaBaru = $request->file('image')->store('public/images');
                $post->is_thumbnail = $foto_namaBaru;
            }

            // Simpan perubahan pada database
            $post->save();
            return redirect()->route('pages')->with('swal', swal_alert('success', 'You are successfully added new records'));
        } else {
            return redirect()->route('pages')->with('swal', swal_alert('error', 'Unexpected error'));
        }
    }

    public function delete($id)
    {
        // Cari data berdasarkan ID
        $post = Pages::find($id);
        // Jika data ditemukan
        if ($post) {
            // Cek apakah ada file di kolom "is_thumbnail"
            if ($post->is_thumbnail) {
                // Hapus file thumbnail dari storage
                Storage::delete($post->is_thumbnail);
            }
            // Hapus data dari database
            $post->delete();
            return redirect()->route('pages')->with('swal', swal_alert('success', 'You are successfully deleted records'));
        } else {
            return redirect()->route('pages')->with('swal', swal_alert('error', 'Data not found'));
        }
    }
}
