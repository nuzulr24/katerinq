@include('components.theme.landing.header')
<section class="container pt-4 pb-4">
  <div class="container">
    <div class="row mb-2 col-12">
      <h4 class="d-flex align-items-center">
        {{ $blog->title }}</h4>
        <nav classs="fs-7" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Beranda </a>
          </li>
          <li class="breadcrumb-item active">
            <a href="#">Berita & Informasi</a>
          </li>
        </ol>
        </nav>
    </div>
    <div class="row pt-2">
        <div class="col-lg-12">
            @if(!empty($blog->is_thumbnail))
                <div class="w-100 mb-3">
                    <img src="{{ assets_url($blog->is_thumbnail) }}" alt="Image"/>
                </div>
            @endif

            <div class="portfolio-description">
              <h6 class="h3 mb-3 font-weight-bold">{{ $blog->title }}</h6>
              <div class="fs-sm pe-3 me-3">Dibuat pada {{ date_formatting($blog->is_created, 'timeago') }}</div>
              <p class="text-justify">{!! $blog->description !!}</p>
            </div>
        </div>
    </div>
  </div>
</section>
@include('components.theme.landing.footer')
