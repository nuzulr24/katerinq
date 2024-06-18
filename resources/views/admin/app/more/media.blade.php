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
                    <h3 class="card-title">Media</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                            <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                <tr>
                                    <th class="min-w-250px">Gambar</th>
                                    <th class="min-w-100px">Jenis Media</th>
                                    <th class="min-w-100px">Tipe Media</th>
                                    <th class="min-w-150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="fw-6 fw-semibold text-gray-600">
                                <tr>
                                    <td><a href="#" class="text-hover-primary text-gray-600">{{ str_replace('public/', '', app_info('logo')) }}</a></td>
                                    <td>Logo</td>
                                    <td><span class="badge badge-light-success fs-7 fw-bold">OK</span></td>
                                    <td><button type="button" class="btn btn-sm btn-primary updateFile" data-media="logo"><i class="ki-outline ki-update-file"></i></button></td>
                                </tr> 
                                <tr>
                                    <td><a href="#" class="text-hover-primary text-gray-600">{{ str_replace('public/', '', app_info('favicon')) }}</a></td>
                                    <td>Icon</td>
                                    <td><span class="badge badge-light-success fs-7 fw-bold">OK</span></td>
                                    <td><button type="button" class="btn btn-sm btn-primary updateFile" data-media="icon"><i class="ki-outline ki-update-file"></i></button></td>
                                </tr>          
                            </tbody>
                        </table>
                    </div>
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
        
        $(document).ready(function() {
            $('.updateFile').click(function() {
                let type = $(this).data('media');
                let name;
                if(type === "logo") {
                    name = "Logo";
                } else {
                    name = "Icon";
                }
                
                $('#generalModal').modal('show');
                $('.customSizing').removeClass('modal-lg').addClass('modal-md');
                $('#generalModalLabel').text(`Perbarui Media - ${name}`)
                $('.result').html(`
                    <form method="POST" action="{{ route('settings.store.media') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Media File<sup class="ms-2 text-danger">*</sup></label>
                            <input type="file" name="image" class="form-control mt-2">
                            <input type="hidden" name="type" value="${type}">
                            <p class="small my-2 text-muted">catatan: hanya menerima file ber-ekstensi jpg,jpeg,png,svg</p>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                `)
            })
        })
    </script>
@endpush
@include('components.theme.pages.footer')