@include('components.theme.pages.header')
<section>
    <div class="row">
        @php
            $url_segment = segment(2);
            if(empty($url_segment)) {
                $url_segment = '';
            }
        @endphp
        <x-theme.pages.account-preference :activeSetting="$url_segment" />
        <div class="col-md-12 mt-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Umum</h3>
                </div>
                <div class="card-body">
                <form action="{{ site_url('user', 'account/update') . '/' . user()['id'] }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-6 form-group">
                            <label class="form-label">Nama Lengkap<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="name" class="form-control form-control-solid mt-2" value="{{ user()->name }}" placeholder="Nama Lengkap">
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label">Alamat Email<sup class="ms-1 text-danger">*</sup></label>
                            <input type="email" name="email" class="form-control form-control-solid mt-2" id="email" value="{{ user()->email }}" placeholder="Alamat Email">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label mb-3">Kata Sandi<span class="ms-2 fs-8 text-muted">opsional (kosongi jika tidak ingin merubah)</span></label>
                        <input type="password" name="password" class="form-control form-control-solid mt-2" value="" placeholder="Kata Sandi">
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label mb-3">Nomer Telepon<sup class="ms-1 text-danger">*</sup></label>
                        <input type="phone" name="phone" class="form-control form-control-solid mt-2" value="{{ user()->phone }}" placeholder="Nomer Telepon">
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ site_url('user', 'account') }}" class="btn btn-light btn-light">Kembali</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
    Inputmask({
        mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
        greedy: false,
        onBeforePaste: function (pastedValue, opts) {
            pastedValue = pastedValue.toLowerCase();
            return pastedValue.replace("mailto:", "");
        },
        definitions: {
            "*": {
                validator: '[0-9A-Za-z!#$%&"*+/=?^_`{|}~\-]',
                cardinality: 1,
                casing: "lower"
            }
        }
    }).mask("#email");
</script>
@endpush
@include('components.theme.pages.footer')