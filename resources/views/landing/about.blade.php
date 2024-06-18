@include('components.theme.landing.header')
<section class="container pt-4 pb-4">
  <div class="container">
    <div class="row mb-2 col-12">
      <h4 class="d-flex align-items-center">
        {{ $data['subtitle'] }}</h4>
        <nav classs="fs-7" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Beranda </a>
          </li>
          <li class="breadcrumb-item active">
            <a href="#">Laman</a>
          </li>
        </ol>
        </nav>
    </div>
    <div class="row pt-2">
        <div class="col-lg-12">
            <div class="portfolio-description">
              <p class="text-justify">{!! app_info('about') !!}</p>
            </div>
        </div>
    </div>
  </div>
</section>
@include('components.theme.landing.footer')