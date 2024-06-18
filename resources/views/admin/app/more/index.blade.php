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
                        <h3 class="card-title">Umum</h3>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'settings.store']) !!}
                        @csrf

                        <div class="row mb-4">
                            <div class="col-6 form-group">
                                <label class="form-label">Judul<sup class="ms-1 text-danger">*</sup></label>
                                <input type="text" name="title" class="form-control form-control-solid mt-2" value="{{ app_info('title') }}" placeholder="Judul">
                            </div>
                            <div class="col-6 form-group">
                                <label class="form-label">Informasi Singkat<sup class="ms-1 text-danger">*</sup></label>
                                <input type="text" name="short_info" class="form-control form-control-solid mt-2" value="{{ app_info('short_info') }}" placeholder="Informasi Singkat">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label mb-3">Tentang Kami</label>
                            <textarea name="about" id="about" class="form-control form-control-solid mt-2">{{ app_info('about') }}</textarea>
                        </div>
                        <div class="separator separator-content separator-dashed my-10 text-muted">Tambahan</div>
                        <div class="row mb-4">
                            <div class="col-4 form-group">
                                <label class="form-label">Alamat Lokasi<span class="ms-2 text-muted fs-8">opsional</span></label>
                                <input type="text" name="address" class="form-control form-control-solid mt-2" value="{{ app_info('address') }}" placeholder="Alamat Lengkap">
                            </div>
                            <div class="col-4 form-group">
                                <label class="form-label">Alamat Email<span class="ms-2 text-muted fs-8">opsional</span></label>
                                <input type="email" name="email" class="form-control form-control-solid mt-2" value="{{ app_info('email') }}" placeholder="Alamat Surel">
                            </div>
                            <div class="col-4 form-group">
                                <label class="form-label">Nomer Whatsapp/Telepon<span class="ms-2 text-muted fs-8">opsional (aktif)</span></label>
                                <input type="text" name="phone" class="form-control form-control-solid mt-2" value="{{ app_info('phone') }}" placeholder="Nomer Telepon/Whatsapp Aktif">
                            </div>
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
@push('scripts')
    <script src="https://preview.keenthemes.com/html/metronic/docs/assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js"></script>
    <script>
        ClassicEditor
        .create(document.querySelector('#about'), {
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