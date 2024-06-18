<div class="container position-relative zindex-3 py-lg-4 pt-md-2 py-xl-5 mb-lg-4">
    <div class="row">
        <div class="col-12">
            <div class="justify-content-center text-center">
                <h2 class="h1 mb-4">Marketplace &#127919;</h2>
                <p class="fs-lg mb-0">Temukan berbagai macam produk katering dari vendor dengan keperluan yang cukup <span class="fw-bold" style="font-style: italic">variatif</span></p>
            </div>
            <div class="mt-5">
                <div class="row">
                    <div class="col form-group pe-0">
                      <input type="text" class="form-control" wire:model="searchFilter" placeholder="Ketik nama website..">
                    </div>
                    <div class="col-auto">
                        <div class="dropdown">
                          <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class='bx bx-filter' style="padding: 0.28rem 0 0.258rem 0 !important"></i>
                          </button>
                          <div class="dropdown-menu px-3 py-3" aria-labelledby="dropdownMenuButton1">
                            <div class="form-group mb-2">
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
                            <div class="form-group mb-2">
                                <label class="form-label mb-3">Lokasi</label>
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
                            <div class="form-group">
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
                                    <a href="{{ url('marketplace') }}" class="btn btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary w-100">Atur Ulang</a>
                                </div>
                            @endif
                          </div>
                        </div>
                        <button type="button" id="reset-filter-button" class="btn btn-secondary d-none">Reset</button>
                    </div>
                </div>
              <p class="my-4 mb-0">Menampilkan data sebanyak <span class="fw-bold">{{ $searchCount }}</span>.</p>
            </div>

            <div class="row mt-4">
                @foreach($sites as $site)
                    <div class="col-md-3 mb-3 pageDetail">
                        <div class="card">
                            <div class="d-md-none d-lg-block">
                                <div class="card-body row p-0 align-items-center">
                                    <div class="col">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="">
                                                <div class="mb-2">
                                                    <img src="{{ assets_url($site->is_thumbnail) }}" alt="thumbnail"/>
                                                </div>
                                                <h4 class="h6 mb-1 fw-bold"><a href="{{ site_url('user', 'product/p') . '/' . $site->id }}">{{ $site->name }}</a></h4>
                                                <p class="card-text d-flex text-muted mb-0">
                                                    <span class="badge bg-primary text-white">
                                                        @if($site->is_type == 1)
                                                            Pre/Weeding
                                                        @elseif($site->is_type == 2)
                                                            Engagement
                                                        @elseif($site->is_type == 3)
                                                            Party
                                                        @else
                                                            Other
                                                        @endif
                                                    </span>
                                                </p>
                                            </div>
                                            @auth
                                                @if(user()->level == 4)
                                                    <div class="ms-auto">
                                                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Keranjang" href="{{ site_url('user', 'sites/add-to-cart') . '/' . $site->id }}" noopener noreferrer>
                                                            <i class="bx bx-basket"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-12 d-md-block d-none mb-2">
            <ul class="pagination pagination-outline mt-5 justify-content-center align-items-center">
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

        <div class="col-12 d-md-none d-block mb-2 justify-content-center align-items-center">
            <a class="btn btn-md btn-outline-primary rounded-2 text-center" href="{{ route('login') }}">Temukan lebih banyak</a>
        </div>
    </div>
</div>
