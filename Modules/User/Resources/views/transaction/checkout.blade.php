@include('components.theme.pages.header')
<section>
    <form method="POST" id="formOrder" enctype="multipart/form-data" action="{{ route('user.account.checkout.place') }}">
    @csrf
    <div class="row">
        <div class="col-md-7 mb-5">
            <div class="card card-flush">
                <div class="card-body">
                    <div class="accordion accordion-icon-collapse" id="kt_accordion_3">
                        @foreach($checkoutItem as $key => $rows)
                            <div class="mb-5">
                                <div class="accordion-header py-3 d-flex" data-bs-toggle="collapse" data-bs-target="#kt_accordion_{{ $key }}_item_1">
                                    <span class="accordion-icon">
                                        <i class="ki-duotone ki-minus-square fs-3 accordion-icon-off"><span class="path1"></span><span class="path2"></span></i>
                                        <i class="ki-duotone ki-plus-square fs-3 accordion-icon-on"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </span>

                                    <h3 class="fs-4 fw-bold mb-0 ms-4">{{ $rows['name'] }}</h3>
                                    <span class="ms-auto text-muted">{{ 'Rp. ' . number_format($rows['items']['price'], 0, ',', '.') }}</span>
                                </div>
                                <div id="kt_accordion_{{ $key }}_item_1" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_3">
                                    <div class="d-flex mt-2 mb-4">
                                        <div class="">
                                            Jenis Produk: {{ $rows['items']['type'] }}
                                        </div>
                                    </div>

                                    <p class="fw-bold">Deskripsi Produk</p>
                                    {!! $rows['items']['description'] !!}
                                </div>
                            </div>
                            @php
                                $itemsId = $key . ',' . $rows['items']['item_id'];
                            @endphp
                            <div class="form-group mb-5">
                                <label class="form-label mb-3">Tanggal Pengiriman<sup class="ms-2 text-danger">(wajib)</sup></label>
                                <input type="date" name="tanggal[{{ $itemsId }}][]" class="form-control form-control-solid mt-2" id="tanggal{{ $key }}" required/>
                            </div>
                            <div class="form-group mb-5">
                                <label class="form-label mb-3">Catatan untuk Merchant<sup class="ms-2 text-danger">(wajib)</sup></label>
                                <textarea class="form-control" name="content[{{ $itemsId }}][]" id="description{{ $key }}" placeholder="Lampirkan catatan disini..."></textarea>
                                <p class="my-2 small text-muted">*catatan: silahkan lampirkan untuk mempermudah merchant dalam mengerjakan pesanan</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Total Item</div>
                        <span class="text-muted fs-6 totalItem">x {{ count($checkoutItem) }}</span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Sub Total</div>
                        <span class="text-gray-900 fw-bolder fs-6">
                            {{ 'Rp. ' . number_format($totalCart, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Total Harga</div>
                        <span class="text-gray-900 fw-bolder fs-6">
                            @php
                                echo 'Rp ' . number_format($totalCart, 0, ',', '.');
                            @endphp
                        </span>
                    </div>

                    <div class="form-group mt-5">
                        <button class="btn btn-primary w-100 placeOrder" type="submit">Bayar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@push('scripts')
<script src="https://preview.keenthemes.com/html/metronic/docs/assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js"></script>
<script>
    const checkboxes = $('input[name="siteCheckbox[]"]');
    const totalPriceInput = $('input[name="totalPrice"]');
    const totalPriceDisplay = $('#totalPriceDisplay');
    const feeDisplay = $('#feeDisplay');
    const totalCostDisplay = $('#totalCostDisplay');

    // Calculate and update the total cost
    function updateTotalCost() {
        let total = parseInt(totalPriceInput.val());
        let subTotal = parseInt(totalPriceInput.val());
        let feeTotal = 0;

        checkboxes.each(function() {
            if ($(this).is(':checked')) {
                const itemPrice = parseInt($(this).data('include-price'));
                total += itemPrice;
                subTotal += itemPrice;

                $('.content[data-item="' + $(this).data('item') + '"]').addClass('d-none');
                $('.custom[data-item="' + $(this).data('item') + '"]').removeClass('d-none');
            } else {
                $('.content[data-item="' + $(this).data('item') + '"]').removeClass('d-none');
                $('.custom[data-item="' + $(this).data('item') + '"]').addClass('d-none');
            }
        });

        feeTotal = total;
        total = subTotal;

        $('.biayaAdmin').html(
            new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(feeTotal).replace(/,00/g, '')
        );
        $('.subTotalHarga').html(
            new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(subTotal).replace(/,00/g, '')
        )
        $('.totalHarga').html(
            new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(total).replace(/,00/g, '')
        );
    }

    // Listen for checkbox changes
    checkboxes.on('change', function() {
        updateTotalCost();
    });

    // Initial update
    updateTotalCost();
</script>
@endpush
@include('components.theme.pages.footer')
