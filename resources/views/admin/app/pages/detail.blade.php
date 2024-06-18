@push('css')
    <style>
        ul {
            list-style: circle !important;
            padding-left: 20px !important;
        }
    </style>
@endpush
@include('components.theme.pages.content.header')

<!-- Konten tampilan spesifik -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="d-flex">
            <a href="{{ app_url('content') }}" class="btn btn-primary">Kembali</a>
        </div>
    </div>
    <div class="col-md-12 mb-4">
    <!-- Konten tampilan spesifik -->
        <div class="my-3">
            <img src="{{ assets_url($data['records']->is_thumbnail) }}" class="img-thumbnail border-0 p-0">
        </div>
        <?= $data['records']->description ?>
    </div>
</div>

@push('scripts')
    
@endpush
@include('components.theme.pages.content.footer')
