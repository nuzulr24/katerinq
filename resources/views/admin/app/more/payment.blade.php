@include('components.theme.pages.header')
<section>
    <div class="row">
        @php
            $url_segment = segment(3);
            if(empty($url_segment)) {
                $url_segment = '';
            }
        @endphp
        <x-theme.pages.setting :activeSetting="$url_segment" />
        <div class="col-md-12 mt-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">API Pembayaran<span class="ms-2 text-gray-400 fs-8">Payment Gateway</span></h3>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'settings.store.payment']) !!}
                    @csrf

                    <div class="row mb-4">
                        <div class="col-6 form-group">
                            <label class="form-label">Merchant ID<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="duitku_merchant" class="form-control form-control-solid mt-2" value="{{ app_info('duitku_merchant') }}" placeholder="Duitku Merchant">
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label">Client Key<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="duitku_client" class="form-control form-control-solid mt-2" value="{{ app_info('duitku_client') }}" placeholder="Duitku Client">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label mb-3">Merchant Product<sup class="ms-1 text-danger">*</sup></label>
                        <select class="form-control form-select-solid mt-2" name="duitku_sandbox">
                            <option value="">-- Pilih --</option>
                            <option value="1" {{ app_info('duitku_sandbox') == 1 ? 'selected' : '' }}>Production</option>
                            <option value="0" {{ app_info('duitku_sandbox') == 0 ? 'selected' : '' }}>Sandbox</option>
                        </select>
                    </div>
                    <div class="my-7 form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" {{ app_info('payment_duitku') == 1 ? 'checked' : '' }} name="payment_duitku" value="1" id="flexSwitchDefault"/>
                        <label class="form-check-label" for="flexSwitchDefault">
                            Aktif
                        </label>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('settings') }}" class="btn btn-light btn-light">Kembali</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</section>
@include('components.theme.pages.footer')