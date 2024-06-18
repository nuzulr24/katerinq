<?php

namespace App\Http\Livewire\Modules\User;

use Livewire\Component;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

use Modules\Seller\Entities\ProductModel as Product;
use Modules\User\Entities\CartModel as Carts;
use App\Enums\GlobalEnum;
use WithPagination;

class Listing extends Component
{
    public $filterType;
    public $categoryFilter;
    public $searchFilter = '';
    public $minimumPrice;
    public $maximumPrice;

    public $currentPage = 1;
    public $perPage = 12;

    public function render(Request $request)
    {
        $valueType = '';
        $valueCategory = '';
        $valueMinimumPrice = '';
        $valueMaximumPrice = '';

        if(!empty($request->input('filterType')) || !empty($request->input('minimumPrice')) || !empty($request->input('maximumPrice')))
        {
            $this->filterType = $request->filterType;
            $this->minimumPrice = $request->minimumPrice;
            $this->maximumPrice = $request->maximumPrice;

            $valueType = $request->filterType;
            $valueMinimumPrice = $request->minimumPrice;
            $valueMaximumPrice = $request->maximumPrice;
        }

        $query = Product::where('is_status', 1);

        if (!empty($this->filterType)) {
            $query->where('is_type', $this->filterType);
        }

        if (!empty($this->minimumPrice) || !empty($this->maximumPrice)) {
            if($this->maximumPrice <= 0) {
                $maximumPrice = 999999999;
            } else {
                $maximumPrice = $this->maximumPrice;
            }

            $query->whereBetween('is_price', [$this->minimumPrice, $maximumPrice]);
        }

        if(!empty($this->minimumPrice)) {
            $query->where('is_price', '<=', $this->minimumPrice);
        }

        if (!empty($this->searchFilter)) {
            $query->where('name', 'like', '%' . $this->searchFilter . '%');
        }

        $sites = $query->paginate($this->perPage)->appends([
            'filterType' => $this->filterType,
            'minimumPrice' => $this->minimumPrice,
            'maximumPrice' => $this->maximumPrice,
        ]);

        $elements = $sites->links()->elements;
        $searchCount = $sites->total();

        return view('livewire.modules.user.product',
            compact('sites', 'elements', 'searchCount', 'valueType', 'valueMinimumPrice', 'valueMaximumPrice')
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
