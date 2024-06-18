@include('components.theme.pages.header')
<section>
    <div class="row">
        <div class="col-md-12 mt-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aktifasi sebagai Reseller</h3>
                </div>
                <div class="card-body">
                <form action="{{ route('user.activate.store', ['user_id' => user()['id']]) }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="form-label mb-3">Nama Bisnis<sup class="ms-1 text-danger">*</sup></label>
                        <input type="text" name="name" class="form-control form-control-solid mt-2" value="{{ old('name') }}" placeholder="Nama Bisnis">
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label mb-3">Nama Alias<sup class="ms-1 text-danger">*</sup></label>
                        <input type="text" name="alias" class="form-control form-control-solid mt-2" value="{{ old('alias') }}" placeholder="Nama Alias, eg: depa.id">
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">Gabung</button>
                        <a href="{{ site_url('user', '/') }}" class="btn btn-light btn-light">Kembali</a>
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