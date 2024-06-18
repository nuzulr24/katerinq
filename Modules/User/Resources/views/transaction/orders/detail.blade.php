@php
    $markComplete = 0;
@endphp
@if($getInfoOrders->is_status == 1 && $getInfoOrders->url_payment != NULL)
    @section('additional')
        <div class="ms-auto me-2">
            <button type="button" class="btn btn-light-danger btn-active-light-danger btn-outline-dashed cancelOrder" data-url="{{ site_url('user', 'orders/cancel') . '/' . $getInfoOrders->id }}">Batalkan</button>
        </div>
    @endsection
@endif
@if(\Modules\User\Entities\OrderHistoryModel::where('buy_id', user()->id)->where('order_id', $getInfoOrders->invoice_number)->where('is_status', 3)->count() > 0 && $getInfoOrders->is_status != 5)
    @section('additional')
    <div class="ms-auto me-2">
        <button type="button" class="btn btn-light-success btn-active-light-success btn-outline-dashed jobDone"><i class="ki-outline ki-check fs-4"></i> Tandai Selesai</button>
    </div>
    @endsection
@endif
@include('components.theme.pages.header')
<section class="invoice">
    <div class="row">
        <div class="col-md-12 mb-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detil Transaksi</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">No. Invoice</div>
                        <span class="text-gray-900 fw-bolder fs-6">{{ $getInfoOrders->invoice_number }}</span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Total Harga</div>
                        <span class="text-gray-900 fw-bolder fs-6">{{ 'Rp. ' . number_format($getInfoOrders->price, 0, ',', '.') }}</span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Tanggal Transaksi</div>
                        <span class="text-gray-900 fw-bolder fs-6">{{ \Carbon\Carbon::parse($getInfoOrders->created_at)->format('d F Y H:i') }}</span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Tanggal Transaksi Diperbarui</div>
                        <span class="text-gray-900 fw-bolder fs-6">{{ \Carbon\Carbon::parse($getInfoOrders->updated_at)->format('d F Y H:i') }}</span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Catatan untuk Merchant</div>
                        <span class="text-gray-900 fw-bolder fs-6">
                            @php
                                $detail = \Modules\User\Entities\OrderHistoryModel::where('order_id', $getInfoOrders->invoice_number)->first();
                            @endphp
                            {!! $detail->comment !!}
                        </span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Status</div>
                        <span class="text-gray-900 fw-bolder fs-6">
                            @if($getInfoOrders->is_status == 1)
                                Menunggu Pembayaran
                            @elseif($getInfoOrders->is_status == 2)
                                Sedang Dikerjakan
                            @elseif($getInfoOrders->is_status == 3)
                                Dikirim oleh Vendor
                            @elseif($getInfoOrders->is_status == 4)
                                Dibatalkan
                            @elseif($getInfoOrders->is_status == 5)
                                Selesai
                            @endif
                        </span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">URL Payment</div>
                        <span class="text-gray-900 fw-bolder fs-6">
                            @if($getInfoOrders->is_status == 1 && $getInfoOrders->url_payment != NULL)
                                <a href="{{ $getInfoOrders->url_payment }}" target="_blank">klik disini</a>
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Transaksi</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data-table" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Vendor</th>
                                    <th>Nama Layanan</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach(\Modules\User\Entities\OrderHistoryModel::where('order_id', $getInfoOrders->invoice_number)->get() as $items)
                                    @if(\App\Models\Seller::where('user_id', $items->seller_id)->exists() == false)
                                        @php
                                            $name = \App\Models\User::where('id', $items->seller_id)->first()->name;
                                        @endphp
                                    @else
                                        @php
                                            $name = \App\Models\Seller::where('user_id', $items->seller_id)->first()->name;
                                        @endphp
                                    @endif
                                    @php
                                        $product = \Modules\Seller\Entities\ProductModel::where('id', $items->product->id)->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $name }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ 'Rp. ' . number_format($items->price, 0, ',', '.') }}
                                        <td>
                                            @php
                                                if ($items->is_status == 1) {
                                                    echo '<span class="mb-1 badge font-medium bg-light-dark text-dark py-3 px-4 fs-7 text-center">Pending</span>';
                                                } elseif($items->is_status == 2) {
                                                    echo '<span class="mb-1 badge font-medium bg-light-primary text-primary py-3 px-4 fs-7 text-center">Dalam pengerjaan</span>';
                                                } elseif($items->is_status == 3) {
                                                    echo '<span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7 text-center">Selesai</span>';
                                                } elseif($items->is_status == 4) {
                                                    echo '<span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7 text-center">Dibatalkan</span>';
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script src="{{ frontend('js/custom/documentation.js') }}"></script>
    <script src="{{ frontend('plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable();
            $('.cancelOrder').click(function() {
                var url = $(this).data('url');
                $('#generalModal').modal('show', {
                    backdrop: 'static'
                });
                $('#generalModalLabel').text('Batalkan Pesanan');
                $('.result').html(`
                    <form method="POST" action="{{ site_url('user', 'orders/cancel') . '/' . $getInfoOrders->id }}">
                        @csrf
                        <div class="form-group mb-4">
                            <div class="alert alert-info">
                                Anda yakin ingin membatalkan pesanan ini? Tekan "Batalkan" untuk membatalkan pesanan ini.
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Batalkan</button>
                            <button type="button" class="btn btn-light btn-light" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                `);
            })

            $('.jobDone').click(function() {
                var url = $(this).data('url');
                $('#generalModal').modal('show', {
                    backdrop: 'static'
                });
                $('#generalModalLabel').text('Tandai Selesai');
                $('.result').html(`
                    <form method="POST" action="{{ site_url('user', 'orders/complete') . '/' . $getInfoOrders->id }}">
                        @csrf
                        <div class="alert alert-info">
                            Pesanan anda telah selesai. Tekan "Tandai Selesai" untuk menandai pesanan ini sebagai selesai.
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Kirim</button>
                            <button type="button" class="btn btn-light btn-light" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                `);
            })
        })
    </script>
@endpush
@include('components.theme.pages.footer')
