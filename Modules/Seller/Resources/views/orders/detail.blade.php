@if($getInfoOrders->is_status == 1)
    @section('additional')
        <div class="ms-auto me-2">
            <button type="button" class="btn btn-light-info btn-active-light-info btn-outline-dashed markAsProgress" data-url="{{ site_url('seller', 'transaction/working') . '/' . $getInfoOrders->order_id }}">Tandai Dalam Progress</button>
        </div>
    @endsection
@elseif($getInfoOrders->is_status == 2)
    @section('additional')
        <div class="ms-auto me-2">
            <button type="button" class="btn btn-light-success btn-active-light-success btn-outline-dashed markAsComplete" data-url="{{ site_url('seller', 'transaction/complete') . '/' . $getInfoOrders->order_id }}">Tandai Selesai</button>
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
                        <span class="text-gray-900 fw-bolder fs-6">{{ $getInfoOrders->order_id }}</span>
                    </div>
                    <div class="separator separator-dashed my-3"></div>

                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Layanan</div>
                        <span class="text-gray-900 fw-bolder fs-6">{{ \Modules\Seller\Entities\ProductModel::where('id', $getInfoOrders->product_id)->first()->name }}</span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Nama Pemesan</div>
                        <span class="text-gray-900 fw-bolder fs-6">{{ \App\Models\User::where('id', $getInfoOrders->buy_id)->first()->name }}</span>
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
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Tanggal Pengiriman </div>
                        <span class="text-gray-900 fw-bolder fs-6">{{ \Carbon\Carbon::parse($getInfoOrders->tanggal)->format('d F Y') }}</span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Catatan untuk Merchant</div>
                        <span class="text-gray-900 fw-bolder fs-6">
                            {!! $getInfoOrders->comment !!}
                        </span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Status</div>
                        <span class="text-gray-900 fw-bolder fs-6">
                            @if($getInfoOrders->is_status == 1)
                                Menunggu Proses
                            @elseif($getInfoOrders->is_status == 2)
                                Pembayaran Diterima, Sedang Dikerjakan
                            @elseif($getInfoOrders->is_status == 3)
                                Dikirim oleh Vendor, sedang menunggu konfirmasi dari klien
                            @elseif($getInfoOrders->is_status == 4)
                                Dibatalkan
                            @elseif($getInfoOrders->is_status == 5)
                                Selesai
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script src="{{ frontend('js/custom/documentation.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.markAsComplete').click(function() {
                var url = $(this).data('url');
                $('#generalModal').modal('show', {
                    backdrop: 'static'
                });
                $('#generalModalLabel').text('Tandai Selesai');
                $('.result').html(`
                    <form method="POST" action="{{ site_url('seller', 'transaction/complete') . '/' . $getInfoOrders->order_id }}">
                        @csrf
                        <div class="alert alert-info">
                            Pesanan klien telah selesai. Tekan "Tandai Selesai" untuk menandai pesanan ini sebagai selesai.
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Kirim</button>
                            <button type="button" class="btn btn-light btn-light" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                `);
            })

            $('.markAsProgress').click(function() {
                var url = $(this).data('url');
                $('#generalModal').modal('show', {
                    backdrop: 'static'
                });
                $('#generalModalLabel').text('Tandai Dalam Proses');
                $('.result').html(`
                    <form method="POST" action="{{ site_url('seller', 'transaction/working') . '/' . $getInfoOrders->order_id }}">
                        @csrf
                        <div class="alert alert-info">
                            Pesanan klien telah dalam proses. Tekan "Tandai Selesai" untuk menandai pesanan ini sebagai selesai.
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
