@include('components.theme.pages.header')

<!-- Konten tampilan spesifik -->
<div class="row">
    <form action="{{ route('content.update', ['id' => $data['records']->id]) }}" id="formPage" method="POST" enctype="multipart/form-data" class="row">
    @csrf
        <div class="col-md-8">
            <div class="form-group">
                <input type="text" name="title" class="form-control mt-1 mb-4 content-title @error('title') is-invalid @enderror" value="{{ $data['records']->title }}" placeholder="Your title anything">
            </div>
            <textarea id="description" name="description" class="@error('description') is-invalid @enderror" rows="4"><?= $data['records']->description ?></textarea>
            <div class="form-group mb-3 mt-3">
                <input type="file" class="form-control" id="images" name="image" accept="image/*">
                <div class="my-2">
                    <?= !empty($data['records']->is_thumbnail) ? '<a data-url="' . assets_url($data['records']->is_thumbnail) . '" href="#" class="previewImage" data-bs-toggle="tooltip" title="Click Here">click here</a>' : 'Thumbnail isnt exist here' ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="exampleEmail1" class="mb-2">Kategori</label>
                        <select class="form-control mt-1 @error('is_category') is-invalid @enderror" name="is_category">
                            <option value="">-- select one --</option>
                            <?php foreach ($data['categories'] as $category) {
                                $selected = $category->id == $data['records']->is_category ? 'selected' : '';
                                $selected_name = $category->id == $data['records']->is_category ? '(selected)' : '';
                            ?>
                                <option value="<?= $category->id ?>" <?= $selected ?>><?= $category->name ?> <?= $selected_name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exampleEmail1" class="mb-3">Tag</label>
                        <select class="form-control mt-1 input-tag @error('is_tags') is-invalid @enderror" multiple="multiple" name="is_tags[]">
                            <?php foreach ($data['tags'] as $tag) { ?>
                                <option value="<?= $tag->name ?>"><?= $tag->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exampleEmail1" class="mb-3">Status</label>
                        <select class="form-control mt-1 @error('is_status') is-invalid @enderror" name="is_status">
                            <?php foreach ([0, 1] as $status) {
                                $name = $status == 0 ? 'Draft' : 'Publish';
                                $selected = $status == $data['records']->is_status ? 'selected' : '';
                                $selected_name = $status == $data['records']->is_status ? '(selected)' : '';
                                ?>
                                <option value="<?= $status ?>" <?= $selected ?>><?= $name ?> <?= $selected_name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exampleEmail1" class="mb-2">Tanggal Dibuat</label>
                        <input type="date" class="form-control mt-1 @error('is_created') is-invalid @enderror" name="is_created" value="{{ $data['records']->is_created }}">
                    </div>
                    <div class="form-group mb-0">
                        <button type="button" class="btn btn-primary w-100 saveContent">Save</button>
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
        $('.input-tag').select2({
            placeholder: 'Pilih salah satu',
            dropdownParent: $('.input-tag').parent()
        });

        @if(!empty($data['records']->is_tags))
            @php
                $tags = explode(',', $data['records']->is_tags);
                $name_tag = [];
                foreach($tags as $value){
                    $name_tag[] = $value;
                }
            @endphp;
            var selectedNames = <?= json_encode($name_tag, true) ?>;
            if (selectedNames.length > 0) {
                selectedNames.forEach(function(name) {
                    $(".input-tag").append(new Option(name, name, true, true)).trigger("change");
                });
            }
        @endif

        $('input[name=is_created]').flatpickr();

        $('.previewImage').click(function () {
            $('#imageModal').modal('show', {
                backdrop: 'static'
            });

            $('#imageModalLabel').html('Preview Image');
            let image = $(this).data('url');
            $('.result').html(`<img src="${image}" class="img-thumbnail w-100 border-0" alt="preview image"/>`);
        });

        $('.saveContent').click(function() {
            $('.saveContent').prop('disabled', true);
            $('.saveContent').html('Saving...');
            setTimeout(function() {
                $('.saveContent').attr('type', 'submit');
                $('#formPage').submit();
            }, 5000)
        })
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
