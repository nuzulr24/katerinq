@include('components.theme.pages.header')
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Umum</h3>
                </div>
                <div class="card-body">
                <form action="{{ site_url('seller', 'account/update') . '/' . user()['id'] }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        @php
                            if(\App\Models\Seller::where('user_id', user()->id)->exists()) {
                                $getDetailSeller = \App\Models\Seller::where('user_id', user()->id)->first();
                                $name = $getDetailSeller->name;
                                $alias = $getDetailSeller->alias;
                                $phone = $getDetailSeller->phone;
                                $address = $getDetailSeller->address;
                                $description = $getDetailSeller->description;
                            } else {
                                $name = '';
                                $alias = '';
                                $phone = '';
                                $address = '';
                                $description = '';
                            }
                        @endphp
                        <div class="col-6 form-group">
                            <label class="form-label">Nama Bisnis<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="name" class="form-control form-control-solid mt-2" value="{{ $name }}" placeholder="Nama Bisnis">
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label">Sebutan Alias<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="alias" class="form-control form-control-solid mt-2" id="alias" value="{{ $alias }}" placeholder="Nama Alias">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6 form-group">
                            <label class="form-label">Nama Lengkap<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="nama" class="form-control form-control-solid mt-2" value="{{ user()->name }}" placeholder="Nama Lengkap">
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label">Email Address<sup class="ms-1 text-danger">*</sup></label>
                            <input type="email" name="email" class="form-control form-control-solid mt-2" id="alias" value="{{ user()->email }}" placeholder="Alamat Email">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6 form-group">
                            <label class="form-label">Alamat Lengkap<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="address" class="form-control form-control-solid mt-2" value="{{ $address }}" placeholder="Alamat Lengkap">
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label">Nomer Telepon Aktif<sup class="ms-1 text-danger">*</sup></label>
                            <input type="number" name="phone" class="form-control form-control-solid mt-2" id="phone" value="{{ $phone }}" placeholder="Nomer Telepon">
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label">Password<sup class="ms-1 text-danger">kosongi jika tidak ingin diubah</sup></label>
                        <input type="password" name="password" class="form-control form-control-solid mt-2" placeholder="Masukkan Password">
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label">Deskripsi Bisnis<sup class="ms-1 text-danger">opsional</sup></label>
                        <textarea class="form-control form-control-solid mt-2" name="description" rows="4" style="resize: none">{{ $description }}</textarea>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ site_url('seller', 'account') }}" class="btn btn-light btn-light">Kembali</a>
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
