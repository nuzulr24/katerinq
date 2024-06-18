<?php

namespace App\Http\Livewire\Modules\User;

use Livewire\Component;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

use App\Models\Content;
use App\Models\ContentCategories as Category;
use App\Enums\GlobalEnum;
use WithPagination;

class Blog extends Component
{
    public $filterType;
    public $categoryFilter;
    public $searchFilter = '';
    
    public $currentPage = 1;
    public $perPage = 10;

    public function render(Request $request)
    {
        $valueCategory = '';

        if(!empty($request->input('categoryFilter')))
        {
            $this->categoryFilter = $request->categoryFilter;

            $valueCategory = $request->categoryFilter;
        }
        
        $query = Content::where('is_status', 1);

        if (!empty($this->categoryFilter)) {
            $query->where('is_category', 'like', '%' . $this->categoryFilter . '%');
        }

        if (!empty($this->searchFilter)) {
            $query->where('title', 'like', '%' . $this->searchFilter . '%');
        }

        $sites = $query->paginate($this->perPage)->appends([
            'categoryFilter' => $this->categoryFilter,
        ]);
        
        $elements = $sites->links()->elements;
        $searchCount = $sites->total();
        $getListCategory = Category::select('*');

        return view('livewire.modules.user.blog', 
            compact('sites', 'elements', 'searchCount', 'getListCategory', 'valueCategory')
        );
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function nextPage()
    {
        $this->currentPage++;
    }

    public function goToPage($page)
    {
        $url = route('user.sites.view', ['id' => $page]);
        return redirect()->to($url);
    }
}