@include('components.theme.pages.header')
<section>
    <div class="row">
        <div class="col-lg-3">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-purchase text-dark fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ $getTotalOrders }}
                    </div>
                    <div class="fw-semibold text-gray-400">
                        Total Pesanan Baru
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-purchase text-danger fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ $getTotalOrdersCancel }}
                    </div>
                    <div class="fw-semibold text-gray-400">
                        Total Pesanan Dibatalkan
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-plus-square text-primary fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ 'Rp. ' . number_format(user()->income, 0, ',', '.') }}
                    </div>
                    <div class="fw-semibold text-gray-400">
                        Total Pendapatan
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-pin text-danger fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ $getTotalWebsite }}
                    </div>
                    <div class="fw-bold text-gray-400">
                        Total Produk
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            @if(\App\Models\Seller::where('user_id', user()->id)->exists() == false)
                <div class="alert alert-info d-flex align-items-center p-5">
                    <div class="alert-text">
                        Anda belum melakukan pendataan sebagai <b>Vendor Party Planner</b>. Silahkan lengkapi profil anda terlebih dahulu dengan klik tombol disamping.
                    </div>
                    <a href="{{ site_url('seller', 'account') }}" class="btn btn-outline btn-light-info ms-auto">Lengkapi Profil</a>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pesanan</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th>No.</th>
                                    <th>Produk</th>
                                    <th>Pemesan</th>
                                    <th>Status Pesanan</th>
                                    <th>Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @if(empty($getListOrder))
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada pesanan...</td>
                                    </tr>
                                @else
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach($getListOrder as $order)
                                        @php
                                            $orders = \Modules\User\Entities\OrderModel::where('invoice_number', $order->order_id)->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $order->product->name }}</td>
                                            <td>{{ $order->client->name }}</td>
                                            <td>
                                                @if ($order->is_status == 1)
                                                    <span class="mb-1 badge font-medium bg-light-dark text-dark py-3 px-4 fs-7 text-center">Pending</span>
                                                @elseif($order->is_status == 2)
                                                    <span class="mb-1 badge font-medium bg-light-info text-info py-3 px-4 fs-7 text-center">Dalam pengerjaan</span>
                                                @elseif($order->is_status == 3)
                                                    <span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7 text-center">Selesai</span>
                                                @elseif($order->is_status == 4)
                                                    <span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7 text-center">Dibatalkan</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($orders->is_status == 1)
                                                    <span class="mb-1 badge font-medium bg-light-dark text-dark py-3 px-4 fs-7 text-center">Belum</span>
                                                @elseif($orders->is_status == 2 || $orders->is_status == 3 || $orders->is_status == 5)
                                                    <span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7 text-center">Lunas</span>
                                                @elseif($orders->is_status == 4)
                                                    <span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7 text-center">Dibatalkan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('components.theme.pages.footer')
