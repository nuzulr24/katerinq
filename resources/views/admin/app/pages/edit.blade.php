@include('components.theme.pages.header')

<!-- Konten tampilan spesifik -->
<div class="row">
    <form action="{{ route('pages.update', $data['records']['id']) }}" method="POST" enctype="multipart/form-data" class="row">
    @csrf
        <div class="col-md-8">
            <div class="form-group">
                <input type="text" name="title" class="form-control mt-1 mb-4 content-title" value="{{ $data['records']['title'] }}" placeholder="Your title anything">
            </div>
            <div class="form-group mb-4">
                <textarea id="description" name="description" value="">{!! $data['records']['description'] !!}</textarea>
            </div>
            <div class="form-group mb-3">
                <label for="exampleEmail1" class="mb-3">Thumbnail</label>
                <label for="images" class="drop-container">
                    <span class="drop-title">Drop files here</span>
                    or
                    <input type="file" class="input_file" id="images" name="image" accept="image/*">
                </label>
                <div class="my-2">
                    <?= !empty($data['records']->is_thumbnail) ? '<a data-url="' . assets_url($data['records']->is_thumbnail) . '" href="#" class="previewImage" data-bs-toggle="tooltip" title="Click Here">click here</a>' : 'Thumbnail isnt exist here' ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="exampleEmail1" class="mb-3">{{ __('page.content.add_new.status') }}</label>
                        <select class="form-control form-select form-select-solid mt-1" name="is_status">
                            <?php foreach ([0, 1] as $status) {
                                $selected = $data['records']['is_status'] == 1 ? 'selected' : '';
                                $name = $status == 0 ? 'Draft' : 'Publish';
                                ?>
                                <option value="<?= $status ?>" {{ $selected }}><?= $name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group mb-5">
                        <label for="exampleEmail1" class="mb-2">{{ __('page.content.add_new.date') }}</label>
                        <input type="date" class="form-control form-control-solid mt-1" placeholder="Pilih tanggal" name="is_created" value="{{ $data['records']['created_at'] }}">
                    </div>
                    <div class="form-group form-check mb-5">
                        <input class="form-check-input" type="checkbox" {{ $data['records']['markAsUnique'] == 1 ? 'checked' : '' }} name="markAsUnique" value="1" id="flexCheckDefault" />
                        <label class="form-check-label" for="flexCheckDefault">
                            Tanda jika Laman Khusus
                        </label>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary w-100 saveContent">{{ __('page.content.add_new.save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script src="https://preview.keenthemes.com/html/metronic/docs/assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js"></script>
    <script>
    $(document).ready(function () {
        $('input[name=is_created]').flatpickr();
    });

    ClassicEditor
    .create(document.querySelector('#description'), {
        toolbar: {
            alignment: {
                options: ['left','right']
            },
            items: [
                'alignment','undo', 'redo',
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
