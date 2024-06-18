<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Content;
use App\Models\ContentTag;
use App\Models\ContentCategories;

use DataTables;
use GuzzleHttp\Client;
use Carbon\Carbon;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Content::select('*');
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('title-post', function($row){
                    $text = '
                    <p class="mb-0">' . $row->title . '</p>
                    <p class="mb-0 small text-muted">Terakhir diperbarui pada ' . date_formatting($row->is_updated, 'timeago') . '</p>
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
                ->addColumn('category', function($row){
                    return $row->category->first()->name;
                })
                ->rawColumns(['title-post','status','category'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Konten',
            'button' => true,
            'module' => [
                'url' => route('content.create'),
                'name' => 'Create New'
            ]
        ];
        
        return view('admin.app.content.blog.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'subtitle' => 'Tambah baru',
            'tags' => ContentTag::all(),
            'categories' => ContentCategories::all(),
        ];
        return view('admin.app.content.blog.add', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'is_status' => 'required',
            'is_created' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,svg|max:7048',
        ], [
            'image.mimes' => 'Tipe file yang diunggah harus jpg, jpeg, png, atau svg.',
        ]);

        if ($validator->fails()) {
           return redirect()->back()->withInput()->with('swal', swal_alert('error', 'Unexpected error, please try again. code: ' . $validator->errors()->first()));
        }

        $input = $request->all();
        $titleSlug = Str::slug($input['title']);
        $foto_namaBaru = null;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $foto_namaBaru = $request->file('image')->store('public/images');
        }

        $post = new Content([
            'slug' => $titleSlug,
            'title' => $input['title'], // Membersihkan input judul menggunakan Purifier
            'description' => $input['description'], // Membersihkan input deskripsi menggunakan Purifier
            'is_tags' => empty($input['is_tags']) ? '' : implode(',', $input['is_tags']),
            'is_category' => empty($input['is_category']) ? '' : $input['is_category'], // Membersihkan input kategori menggunakan Purifier
            'is_status' => $input['is_status'],
            'is_created' => $input['is_created'], // Membersihkan input tanggal menggunakan Purifier
            'is_thumbnail' => $foto_namaBaru,
        ]);

        $check = Content::where('title', $input['title'])->count();
        if ($check == 0) {
            if ($post->save()) {
                return redirect()->route('content')->with('swal', swal_alert('success', 'You have successfully added data'));
            } else {
                return redirect()->route('content')->with('swal', swal_alert('error', 'An error occurred in the query'));
            }
        } else {
            return redirect()->route('content')->with('swal', swal_alert('error', 'Title already exists'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = [
            'subtitle' => Content::where('id', $id)->first()->title,
            'records' => Content::where('id', $id)->first()
        ];
        return view('admin.app.content.blog.detail', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [
            'subtitle' => 'Edit: ' . Content::where('id', $id)->first()->title,
            'records' => Content::where('id', $id)->first(),
            'categories' => ContentCategories::all(),
            'tags' => ContentTag::all()
        ];
        $posts = Content::FindOrFail($id);

        return view('admin.app.content.blog.edit', compact('data','posts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi input sebelum memperbarui data
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'is_status' => 'required',
            'is_created' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,svg|max:7048',
        ], [
            'image.mimes' => 'Tipe file yang diunggah harus jpg, jpeg, png, atau svg.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('swal', swal_alert('error', 'Unexpected error, please try again. code: ' . $validator->errors()->first()));
        }

        // Cari data berdasarkan ID
        $post = Content::find($id);

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
            $post->is_tags = empty($request->input('is_tags')) ? '' : implode(',', $request->input('is_tags'));
            $post->is_category = empty($request->input('is_category')) ? '' : $request->input('is_category');
            $post->is_status = $request->input('is_status');
            $post->is_created = $request->input('is_created');

            // Jika ada file baru yang diunggah, simpan file baru di storage
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $foto_namaBaru = $request->file('image')->store('public/images');
                $post->is_thumbnail = $foto_namaBaru;
            }

            // Simpan perubahan pada database
            $post->save();
            return redirect()->route('content')->with('swal', swal_alert('success', 'You are successfully added new records'));
        } else {
            return redirect()->route('content')->with('swal', swal_alert('error', 'Unexpected error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Cari data berdasarkan ID
        $post = Content::find($id);
        // Jika data ditemukan
        if ($post) {
            // Cek apakah ada file di kolom "is_thumbnail"
            if ($post->is_thumbnail) {
                // Hapus file thumbnail dari storage
                Storage::delete($post->is_thumbnail);
            }
            // Hapus data dari database
            $post->delete();
            return redirect()->route('content')->with('swal', swal_alert('success', 'You are successfully deleted records'));
        } else {
            return redirect()->route('content')->with('swal', swal_alert('error', 'Data not found'));
        }
    }

    // tags
    public function tags(Request $request)
    {
        if ($request->ajax()) {
            $data = ContentTag::select('*');
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $edit = route('content.tag.edit', ['id' => $row->id]);
                    $delete = route('content.tag.delete', ['id' => $row->id]);
                    $btn = '
                    <a href="' . $edit . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-pencil"></i></a>
                    <a href="' . $delete . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-trash"></i></a>
                    ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Tags',
            'button' => true,
            'module' => [
                'url' => route('content.tag.create'),
                'name' => 'Create New'
            ]
        ];
        
        return view('admin.app.content.tag.index', compact('data'));
    }
    
    public function addTags()
    {
        $data = [
            'subtitle' => 'Create New Tags',
        ];
        
        return view('admin.app.content.tag.add', compact('data'));
    }

    public function editTags($id)
    {
        $tag = ContentTag::find($id);

        if (!$tag) {
            return redirect()->route('content.tag')->with('swal', swal_alert('error', 'Tag not found.'));
        }

        $data = [
            'subtitle' => 'Edit Tags',
            'tag' => $tag, // Assuming you have $tag data from the database
        ];
    
        return view('admin.app.content.tag.edit', compact('data', 'id')); // Pass the $id to the view
    }

    public function createTags(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        // Check if a tag with the same name already exists
        $tag = ContentTag::where('name', $request->input('name'))->first();

        if ($tag) {
            return redirect()->route('content.tag')->with('swal', swal_alert('error', 'There are tags already there.'));
        }

        $tag = new ContentTag([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')), // Menggunakan slug otomatis
        ]);

        if ($tag->save()) {
            return redirect()->route('content.tag')->with('swal', swal_alert('success', 'Tag added successfully.'));
        } else {
            return redirect()->route('content.tag')->with('swal', swal_alert('error', 'An error occurred while adding a tag.'));
        }
    }

    public function updateTags(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        // Check if a tag with the same id exists
        $tag = ContentTag::find($id);

        if (!$tag) {
            return redirect()->route('content.tag')->with('swal', swal_alert('error', 'Tag not found.'));
        }

        $tag->name = $request->input('name');
        $tag->slug = Str::slug($request->input('name')); // Menggunakan slug otomatis

        if ($tag->save()) {
            return redirect()->route('content.tag')->with('swal', swal_alert('success', 'Tag updated successfully.'));
        } else {
            return redirect()->route('content.tag')->with('swal', swal_alert('success', 'An error occurred while updating the tag.'));
        }
    }

    public function deleteTag($id)
    {
        $tag = ContentTag::find($id);

        if ($tag) {
            $tag->delete();
            return redirect()->route('content.tag')->with('swal', swal_alert('success', 'Tag deleted successfully.'));
        } else {
            return redirect()->route('content.tag')->with('swal', swal_alert('success', 'Tag not found.'));
        }
    }

    // categories
    public function categories(Request $request)
    {
        if ($request->ajax()) {
            $data = ContentCategories::select('*');
            // Convert the Eloquent Collection to a regular PHP array
            $data->each(function ($item, $key) {
                $item->rowIndex = $key + 1;
            });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $edit = route('content.categories.edit', ['id' => $row->id]);
                    $delete = route('content.categories.delete', ['id' => $row->id]);
                    $btn = '
                    <a href="' . $edit . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-pencil"></i></a>
                    <a href="' . $delete . '" class="btn btn-light btn-sm px-4"><i class="ki-outline ki-trash"></i></a>
                    ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data = [
            'subtitle' => 'Categories',
            'button' => true,
            'module' => [
                'url' => route('content.categories.create'),
                'name' => 'Create New'
            ]
        ];
        
        return view('admin.app.content.categories.index', compact('data'));
    }

    public function addCategories()
    {
        $data = [
            'subtitle' => 'Create New Categories',
        ];
        
        return view('admin.app.content.categories.add', compact('data'));
    }

    public function editCategories($id)
    {
        $tag = ContentCategories::find($id);

        if (!$tag) {
            return redirect()->route('content.categories')->with('swal', swal_alert('error', 'Categories not found.'));
        }

        $data = [
            'subtitle' => 'Edit Categories',
            'tag' => $tag, // Assuming you have $tag data from the database
        ];
    
        return view('admin.app.content.categories.edit', compact('data', 'id')); // Pass the $id to the view
    }

    public function createCategories(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        // Check if a tag with the same name already exists
        $tag = ContentCategories::where('name', $request->input('name'))->first();

        if ($tag) {
            return redirect()->route('content.categories')->with('swal', swal_alert('error', 'There are categories already there.'));
        }

        $tag = new ContentCategories([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')), // Menggunakan slug otomatis
        ]);

        if ($tag->save()) {
            return redirect()->route('content.categories')->with('swal', swal_alert('success', 'Categories added successfully.'));
        } else {
            return redirect()->route('content.categories')->with('swal', swal_alert('error', 'An error occurred while adding a categories.'));
        }
    }

    public function updateCategories(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        // Check if a tag with the same id exists
        $tag = ContentCategories::find($id);

        if (!$tag) {
            return redirect()->route('content.categories')->with('swal', swal_alert('error', 'Categories not found.'));
        }

        $tag->name = $request->input('name');
        $tag->slug = Str::slug($request->input('name')); // Menggunakan slug otomatis

        if ($tag->save()) {
            return redirect()->route('content.categories')->with('swal', swal_alert('success', 'Categories updated successfully.'));
        } else {
            return redirect()->route('content.categories')->with('swal', swal_alert('success', 'An error occurred while updating the categories.'));
        }
    }

    public function deleteCategories($id)
    {
        $tag = ContentCategories::find($id);

        if ($tag) {
            $tag->delete();
            return redirect()->route('content.categories')->with('swal', swal_alert('success', 'Categories deleted successfully.'));
        } else {
            return redirect()->route('content.categories')->with('swal', swal_alert('success', 'Categories not found.'));
        }
    }
}
