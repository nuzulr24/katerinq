@include('components.theme.pages.header')
<section>
    <div class="row">
        <div class="col-md-6 mb-2">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-purchase text-dark fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ $getTotalOrders }}
                    </div>
                    <div class="fw-semibold text-gray-400">
                        Total Pesanan
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-plus-square text-primary fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ 'Rp. ' . number_format($getListPengeluaran, 0, ',', '.') }}
                    </div>
                    <div class="fw-semibold text-gray-400">
                        Pengeluaran anda
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pesanan Anda</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                            <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                <tr>
                                    <th class="min-w-100px">No</th>
                                    <th class="min-w-150px">No. Invoice</th>
                                    <th class="min-w-150px">Status</th>
                                    <th class="min-w-150px">Biaya</th>
                                    <th class="min-w-150px">Waktu</th>
                                    <th class="min-w-150px">Aksi</th>
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
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $order->invoice_number }}</td>
                                            <td>
                                                @if ($order->is_status == 1)
                                                    <span class="mb-1 badge font-medium bg-light-dark text-dark py-3 px-4 fs-7 text-center">Pending</span>
                                                @elseif($order->is_status == 2)
                                                    <span class="mb-1 badge font-medium bg-light-info text-info py-3 px-4 fs-7 text-center">Dalam pengerjaan</span>
                                                @elseif($order->is_status == 3)
                                                    <span class="mb-1 badge font-medium bg-light-primary text-primary py-3 px-4 fs-7 text-center">Dikirim oleh Vendor</span>
                                                @elseif($order->is_status == 4)
                                                    <span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7 text-center">Dibatalkan</span>
                                                @elseif($order->is_status == 5)
                                                    <span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7 text-center">Selesai</span>
                                                @endif
                                            </td>
                                            <td>{{ 'Rp. ' . number_format($order->price, 0, ',', '.') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</td>
                                            <td>
                                                <a href="{{ site_url('user', 'orders/v') . '/' . $order->id }}" class="btn btn-sm btn-light"><i class="ki-outline ki-arrow-up-right fs-4"></i></a>
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
