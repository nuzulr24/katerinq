<div class="row">
    <div class="col-12">
        <div class="my-4 mb-2">
            <input wire:model="searchFilter" type="text" class="form-control form-control-solid form-control-lg py-4" placeholder="Search...">
            <div class="d-flex mt-4 align-items-center">
                <div class=""><p class="mb-0">Menampilkan data sebanyak <span class="fw-bold">{{ $searchCount }}</span>.</p></div>
                <div class="ms-auto">
                    <button class="btn btn-outline btn-sm justify-content-end"
                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                        data-kt-menu-overflow="true">
                        <i class="ki-outline ki-filter fs-7 me-n1"></i>
                        <span class="mb-0">Filter</span>
                    </button>

                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-300px"
                        data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Atur berdasarkan</div>
                        </div>
                        <div class="separator mb-3 opacity-75"></div>
                        <div class="px-7 py-5 menu-item" data-kt-docs-table-filter="status_type">
                            <div class="form-group mb-5">
                                <label class="form-label mb-3">Tipe</label>
                                <select class="form-select form-select-solid" wire:model="filterType">
                                    <option value="">Pilih salah satu</option>
                                    @foreach ([1,2] as $type)
                                        @if (!empty($valueType))
                                            @php $selected = $type == $valueType ? 'selected' : '' @endphp
                                            @php $name = $type == 1 ? 'Single' : 'Bundling' @endphp
                                            <option value="{{ $type }}" {{ $selected }}>{{ $name }}</option>
                                        @else
                                            @php $name = $type == 1 ? 'Single' : 'Bundling' @endphp
                                            <option value="{{ $type }}">{{ $name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-5">
                                <label class="form-label mb-3">Harga</label>
                                <div class="row">
                                    <div class="col">
                                        <input type="number" min="0" max="50000000" wire:model="minimumPrice" class="form-control form-control-solid" value="{{ !empty($valueMinimumPrice) ? $valueMinimumPrice : 0 }}"/>
                                    </div>
                                    <div class="col">
                                        <input type="number" min="0" max="50000000" wire:model="maximumPrice" class="form-control form-control-solid" value="{{ !empty($valueMaximumPrice) ? $valueMinimumPrice : 0 }}"/>
                                    </div>
                                </div>
                            </div>
                            @if(filterExists())
                                <div class="mt-4">
                                    <a href="{{ site_url('user', 'product') }}" class="btn btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary w-100">Atur Ulang</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="my-8 row">
            @foreach($sites as $site)
                <div class="col-md-3 mb-3 pageDetail">
                    <div class="card">
                        <div class="card-body">
                            @if(!empty($site->thumbnail))
                                <img src="{{ assets_url($site->thumbnail) }}" class="w-100 rounded mb-4" alt="Background"/>
                            @endif
                            <div class="mb-10">
                                <h4 class="card-title fw-bold"><a href="{{ site_url('user', 'product/p') . '/' . $site->id }}">{{ $site->name }}</a></h4>
                                <p class="card-text d-flex text-muted mb-0">
                                    @if($site->is_type == 1)
                                        <span class="badge mt-2 bg-light-primary text-primary">Single</span>
                                    @else
                                        <span class="badge mt-2 bg-light-danger text-danger">Bundling</span>
                                    @endif
                                    <span class="badge mt-2 ms-2 bg-light-dark text-dark"><i class="ki-solid ki-user-square me-2"></i>{{ $site->merchant->name }}</span>
                                </p>
                            </div>
                            <div class="d-flex ms-auto align-items-center">
                                <div class="text-center">
                                    <h4 class="h6 fw-bold">Est. Waktu</h4>
                                    <p class="card-text text-muted mb-0">{{ $site->is_delivery_time }} hari</p>
                                </div>
                                <div class="text-center ms-5 me-5">
                                    <h4 class="h6 fw-bold">Harga</h4>
                                    <p class="card-text text-muted mb-0">{{ rupiah_changer($site->is_price) }}</p>
                                </div>
                                <div class="text-center">
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Keranjang" href="{{ site_url('user', 'product/add-to-cart') . '/' . $site->id }}" noopener noreferrer>
                                        <i class="ki-duotone ki-handcart fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


        <ul class="pagination pagination-outline">
            @if ($sites->onFirstPage())
                <li class="page-item previous disabled"><span class="page-link"><i class="previous"></i></span></li>
            @else
                <li class="page-item"><a href="{{ $sites->previousPageUrl() }}" class="page-link"><i class="previous"></i></a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item"><span class="page-link">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @php
                            $pattern = '/\/livewire\/message\/([A-Za-z0-9_\.]+)\?/';
                            $replacement = '/user/sites?';
                            $urls = preg_replace($pattern, $replacement, $url);
                        @endphp
                        @if ($page == $sites->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a href="{{ $urls }}" class="page-link">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($sites->hasMorePages())
                <li class="page-item"><a href="{{ $sites->nextPageUrl() }}" class="page-link"><i class="next"></i></a></li>
            @else
                <li class="page-item next disabled"><span class="page-link"><i class="next"></i></span></li>
            @endif
        </ul>
    </div>
</div>
