@include('components.theme.pages.orders.header')
<section class="invoice">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Produk</h3>
                </div>
                <div class="card-body">
                    @if(!empty($sites->thumbnail))
                        <img src="{{ assets_url($sites->thumbnail) }}" class="w-100 rounded mb-4" alt="Background"/>
                    @endif
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Merchant</div>
                        <span class="text-gray-900 fw-bolder fs-6">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-25px symbol-circle">
                                    <div class="symbol-label" style="background-image:url({{ gravatar_team(!empty($sites->user->email) ? $sites->user->email : 'random' . rand(111, 999) . '@gmail.com') }}"></div>
                                </div>
                                <div class="ms-3"><span>
                                    @if(\App\Models\Seller::where('user_id', $sites->user_id)->exists() == false)
                                        {{ $sites->user->name }}
                                    @else
                                        {{ \App\Models\Seller::where('user_id', $sites->user_id)->first()->name }}
                                    @endif
                                </span></div>
                            </div>
                        </span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Tipe</div>
                        <span class="text-gray-900 fw-bolder fs-6">
                            @if($sites->is_type == 1)
                                Single
                            @else
                                Bundling
                            @endif
                        </span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Estimasi Waktu</div>
                        <span class="text-gray-900 fw-bolder fs-6">{{ $sites->is_delivery_time }} hari</span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Harga</div>
                        <span class="text-gray-900 fw-bolder fs-6">{{ 'Rp ' . number_format($sites->is_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('components.theme.pages.footer')
