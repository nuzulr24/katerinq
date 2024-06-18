@if($payment->is_status == 1)
    @section('additional')
        <div class="card-toolbar">
            <button class="btn btn-sm btn-light-danger border border-dotted cancelOrder" data-invoice="{{ $payment->id }}">Batalkan</button>
        </div>
    @endsection
@elseif($payment->is_status == 3)
    @section('additional')
        <div class="card-toolbar">
            <button class="btn btn-sm btn-light-primary border border-dotted viewPayment" data-payment="{{ $payment->is_attachment }}">Bukti Pembayaran</button>
        </div>
    @endsection
@endif
@include('components.theme.pages.header')
<section class="invoice">
    <div class="row">
        <div class="col-md-7 mb-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Secure Code</div>                   
                        <span class="text-gray-900 fw-bolder fs-6">{{ $payment->id }}</span> 
                    </div>
                    
                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Invoice ID</div>                   
                        <span class="text-gray-900 fw-bolder fs-6">{{ $payment->invoice_id }}</span> 
                    </div>

                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Nominal Deposit</div>                   
                        <div class="d-flex align-items-senter">
                            <i class="ki-outline ki-arrow-up-right fs-2 text-success me-2"></i>                  
                            <span class="text-gray-900 fw-bolder fs-6">{{ 'Rp ' . number_format($payment->amount, 0, ',', '.') }}</span> 
                        </div>  
                    </div>

                    <div class="separator separator-dashed my-3"></div>                
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Terakhir Diperbarui</div>                   
                        <span class="text-gray-900 fw-bolder fs-6">{{ date_formatting($payment->updated_at, 'timeago') }}</span> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5 mb-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambahan</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Status</div>                   
                        <span class="fs-6">
                            @if ($payment->is_status == enum('isWithdrawPending'))
                                <span class="badge badge-light-dark">Menunggu Pembayaran</span>
                            @elseif($payment->is_status == enum('isWithdrawOnProgress'))
                                <span class="badge badge-light-info">Sedang Proses</span>
                            @elseif($payment->is_status == enum('isWithdrawPaid'))
                                <span class="badge badge-light-success">Telah dibayarkan</span>
                            @elseif($payment->is_status == enum('isWithdrawCancel'))
                                <span class="badge badge-light-danger">Dibatalkan</span>
                            @endif
                        </span> 
                    </div>

                    <div class="separator separator-dashed my-3"></div>                
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Invoice Dibuat</div>                   
                        <span class="text-gray-900 fw-bolder fs-6">{{ date('Y-m-d', strtotime($payment->created_at)) }}</span> 
                    </div>

                    <div class="separator separator-dashed my-3"></div>                
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Metode</div>                   
                        <span class="text-gray-900 fw-bolder fs-6">
                            @php
                                $checkBank = \Modules\Seller\Entities\RekeningBankModel::where('id', $payment->rekening->first()->rid)->first();
                            @endphp
                            {{ $checkBank->nama }}
                        </span> 
                    </div>

                    <div class="separator separator-dashed my-3"></div>                
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Issued By</div>                   
                        <span class="text-gray-900 fw-bolder fs-6">{{ user()->name }}</span> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script> 
    $(document).ready(function() {
        $('.input-bayar').select2({
            placeholder: 'Pilih salah satu',
        });

        $('.cancelOrder').click(function() {
            Swal.fire({
                title: 'Apakah anda?',
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'btn btn-primary',
                cancelButtonColor: 'btn btn-danger',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Dibatalkan!',
                        'Anda membatalkan transaksi ini.',
                        'success'
                    )

                    setTimeout(() => {
                        window.location.href = "{{ site_url('seller', 'account/withdrawal/c') }}/" + $(this).data('invoice');
                    }, 1000)
                }
            })
        });
        
        @if($payment->is_status == enum('isWithdrawPaid'))
            $('.viewPayment').on('click', () => {
                $('#viewerModal').modal('show');
                $('.result').html(`
                    <div class="">
                        <img src="{{ assets_url($payment->is_attachment) }}" class="img-responsive w-100">
                    </div>
                `);
            })
        @endif
    })
</script>
@endpush
@include('components.theme.pages.footer')