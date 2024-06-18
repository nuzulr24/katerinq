@include('components.theme.pages.header')
<!-- Konten tampilan spesifik -->
<div class="row">
    <div class="col-md-12 mb-4">
    <!-- Konten tampilan spesifik -->
        @if(!empty($data['records']->is_thumbnail))
            <div class="my-3">
                <img src="{{ assets_url($data['records']->is_thumbnail) }}" class="img-thumbnail border-0 p-0">
            </div>
        @endif
        <?= $data['records']->description ?>
    </div>
</div>
@include('components.theme.pages.footer')
