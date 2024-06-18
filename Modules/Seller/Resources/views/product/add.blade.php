@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Formulir Produk</h6>
                    <div class="card-toolbar">
                        <i class="ki-duotone ki-information-2 fs-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Tolong dilengkapi data informasi dengan valid">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ site_url('seller', 'product/store') }}" enctype="multipart/form-data">
                    @csrf
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk<sup class="text-danger">*</sup></label>
                                <input type="text" name="name" value="{{ old('name') }}" autocomplete="off" class="form-control form-control-solid mt-2 {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Masukkan Nama Produk">
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Produk<sup class="text-danger">*</sup></label>
                                <select name="type" class="form-select form-select-solid mt-2 {{ $errors->has('type') ? 'is-invalid' : '' }}">
                                    <option value="">Pilih Jenis</option>
                                    @foreach([1,2] as $type)
                                        @php
                                            $name = $type == 1 ? 'Single' : 'Bundling';
                                            $select = $type == old('type') ? 'selected' : '';
                                        @endphp
                                        <option value="{{ $type }}" {{ $select }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label mb-4">Deskripsi<sup class="text-danger">*</sup></label>
                            <textarea name="description" id="description" class="form-control form-control-solid mt-2 {{ $errors->has('description') ? 'is-invalid' : '' }}">{{ old('description') }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label mb-4">Harga<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                <input type="number" class="form-control {{ $errors->has('is_price') ? 'is-invalid' : '' }}" name="is_price" value="{{ old('is_price') }}" min="0" placeholder="0"/>
                            </div>
                            @error('is_price') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label mb-4">Estimasi Waktu<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <input type="number" class="form-control {{ $errors->has('is_delivery_time') ? 'is-invalid' : '' }}" name="is_delivery_time" value="{{ old('is_delivery_time') }}" min="0" placeholder="0"/>
                                <span class="input-group-text" id="basic-addon2">hari</span>
                            </div>
                            @error('is_delivery_time') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label mb-4">Thumbnail</label>
                            <input type="file" class="form-control form-control-solid" name="image">
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">Tambah</button>
                            <a href="{{ site_url('seller', 'product') }}" class="btn btn-light btn-light">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script src="https://preview.keenthemes.com/html/metronic/docs/assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js"></script>
    <script>
    $(document).ready(function () {
        $('.input-category').select2({
            placeholder: 'Pilih salah satu',
            dropdownParent: $('.input-category').parent()
        });

        $(".checkbox").change(function() {
            if(this.checked) {
                $('.form-custom-content').removeClass('d-none');
            } else {
                $('.form-custom-content').addClass('d-none');
            }
        });
    });

    ClassicEditor
    .create(document.querySelector('#description'), {
        toolbar: {
            alignment: {
                options: ['left','right']
            },
            items: [
                'undo', 'redo',
                '|', 'heading',
                '|', 'bold', 'italic', 'underline', 'strikethrough',
                '|', 'link', 'insertTable',
                '|', 'bulletedList', 'numberedList', 'outdent', 'indent',
                '|', 'alignment','blockQuote',
                '|', 'horizontalLine',
            ]
        }
    });
    </script>
@endpush
@include('components.theme.pages.footer')
