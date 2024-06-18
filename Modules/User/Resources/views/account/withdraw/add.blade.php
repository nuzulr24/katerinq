@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    {{ Form::open(['route' => 'withdrawal.store', 'id' => 'form-deposit']) }}
    @csrf
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Formulir Tarik Tunai</h6>
                    <div class="card-toolbar">
                        <i class="ki-duotone ki-information-2 fs-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Tolong dilengkapi data informasi dengan valid">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-4">
                            <span class="required">Opsi</span>
                            <span class="ms-1" data-bs-toggle="tooltip" title="Pilih salah satu">
                                <i class="ki-duotone ki-information text-gray-500 fs-7"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </span>
                        </label>

                        <div class="d-flex flex-stack gap-5 mb-4">
                            <button type="button" class="btn btn-light-primary w-100 pick-amount" data-kt-docs-advanced-forms="interactive" data-amount="250000">250000</button>
                            <button type="button" class="btn btn-light-primary w-100 pick-amount" data-kt-docs-advanced-forms="interactive" data-amount="500000">500000</button>
                            <button type="button" class="btn btn-light-primary w-100 pick-amount" data-kt-docs-advanced-forms="interactive" data-amount="1000000">1000000</button>
                        </div>
                        
                        <input type="hidden" name="action" value=""/>
                        <input type="number" class="form-control form-control-solid py-5" placeholder="Masukkan Jumlah" name="amount" />
                        <p class="small mb-0 mt-3">Catatan: Minimal penarikan saldo <span class="fw-bolder">Rp. 250.000</span></p>
                    </div>
                    <div class="form-group mt-4 mb-0">
                        <button type="button" class="btn btn-primary w-100 btnDeposit">Proses</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Ringkasan</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="form-label pb-1">Rekening</label>
                        <select class="form-select form-select-solid form-select-sm mb-1 input-bayar" name="is_account">
                            @foreach($getListOfBank as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->account_number . ' (' . $bank->name . ')' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="d-flex list-group-item px-0 pb-3">
                            <span class="fw-bold">Saldo <i class="ki-outline ki-information-2 fs-9 ms-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Saldo anda tersedia senilai {{ 'Rp. ' . number_format(user()->income, 0, ',', '.') }}"></i></span>
                            <span class="ms-auto">{{ rupiah_changer(user()->campaign) }}</span>
                        </li>
                        <li class="d-flex list-group-item px-0 pb-3 pt-3">
                            <span class="fw-bold">Nominal Ditarik</span>
                            <span class="ms-auto amount">0</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</section>

@push('scripts')
    <script>
    $(document).ready(function () {
        $('.input-bayar').select2({
            placeholder: 'Pilih salah satu',
        })
        const options = document.querySelectorAll('[data-kt-docs-advanced-forms="interactive"]');
        const inputEl = document.querySelector('[name="amount"]');
        options.forEach(option => {
            option.addEventListener('click', e => {
                e.preventDefault();
                inputEl.value = e.target.innerText;
                
                const amount = parseInt(e.target.innerText);
                var formatAmount = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(amount);
                $('.amount').html(formatAmount.replace(/,00/g, ''));
            });
        });

        $('input[name=amount]').change(function() {
            const amount = parseInt($(this).val());
            var formatAmount = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
            $('.amount').html(formatAmount.replace(/,00/g, ''));
        });

        $('.btnDeposit').click(function() {
            if($('input[name=amount]').val() === '' && $('.input-bayar').val() === '') {
                Swal.fire({
                    text: "Nominal dan rekening tidak boleh kosong",
                    icon: "error"
                });
            } else {
                $(this).attr('disabled', 'disabled');
                $(this).html(`
                    <div class="spinner-border text-white mb-n1" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `);

                const amount = $('input[name=amount]').val();
                var amountBalance = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(amount).replace(/,00/g, '');

                $('#transaksiModal').modal('show', {
                    backdrop: 'static'
                });
                $('#transaksiModalLabel').html('Konfirmasi Penarikan Tunai');
                $('.result').html(`
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Nominal yang akan ditarik</div>                   
                        <div class="d-flex align-items-senter">
                            <i class="ki-outline ki-arrow-up-right fs-2 text-success me-2"></i>                  
                            <span class="text-gray-900 fw-bolder fs-6">${amountBalance}</span> 
                        </div>  
                    </div>

                    <div class="separator separator-dashed my-3"></div>                
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">Saldo tersedia</div>                   
                        <div class="d-flex align-items-senter">
                            <i class="ki-outline ki-arrow-down-right fs-2 text-danger me-2"></i>                                              
                            <span class="text-gray-900 fw-bolder fs-6">{{ 'Rp. ' . number_format(user()->income, 0, ',', '.') }}</span> 
                        </div>  
                    </div>
                `);

                $('.buttonOrder').html(`
                    <button class="btn btn-outline btn-primary me-2 placeDeposit" data-button="tarik-sekarang" type="button">Tarik Sekarang</button>
                    <button class="btn btn-outline btn-outline-dashed me-2" data-bs-dismiss="modal" type="button">Batal</button>
                `);

                setTimeout(() => {
                    $(this).removeAttr('disabled');
                    $(this).html('Proses');
                }, 4000);
            }

            $('.placeDeposit').click(function() {
                const action = $(this).data('button');
                if(action === "tarik-sekarang") {
                    $('input[name=action]').val(1);
                    $('#form-deposit').submit();
                }
            })
        })
    });
    </script>
@endpush
@include('components.theme.pages.footer')