@include('components.theme.landing.header')
<section class="container pt-4">
  <div class="container">
    <div class="row mb-4 col-12">
      <h4 class="d-flex align-items-center">
        @if(isUrlSecure($sites->url))
            <i class='bx bxs-check-shield text-success me-2'></i>
        @else
            <i class='bx bx-shield-x text-danger'></i>
        @endif
          {{ removeUrlPrefix($sites->url) }}</h4>
      <nav classs="fs-7" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Beranda </a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('marketplace') }}">Website </a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Tentang Situs</li>
        </ol>
      </nav>
    </div>
    <div class="row pt-4">
        <div class="col-lg-8">
            <div class="portfolio-description">
              <h4>Tentang Situs</h3>
              <p id="website-description">{!! empty($sites->description) ? '-' : $sites->description !!}</p>
            </div>
        </div>
        <div class="col-lg-4 position-relative">
            <div class="sticky-top ms-xl-5 ms-lg-4 ps-xxl-4" style="top: 105px !important;">
                <div class="card">
                    <div class="card-body text-center"><h1 class="h5 card-title mb-0">&#129309; Tentang Situs</h1></div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex b-0">
                            <span>Penjual</span>
                            <span class="ms-auto" id="website-seller">{{ $sites->user->first()->name }}</span>
                        </li>
                        <li class="list-group-item d-flex">
                            <span>Harga Guest Post</span>
                            <span class="ms-auto" id="website-price">{{ rupiah_changer($sites->is_post_price) }}</span>
                        </li>
                        @if($sites->is_content_included == 1)
                        <li class="list-group-item d-flex">
                            <span>Harga Konten</span>
                            <span class="ms-auto" id="website-content_price">{{ rupiah_changer($sites->is_content_price) }}</span>
                        </li>
                        @endif
                        <li class="list-group-item d-flex">
                            <span>Est. Pengerjaan</span>
                            <span class="ms-auto">{{ $sites->is_delivery_time }} hari</span>
                        </li>
                        <li class="list-group-item d-flex">
                            <span>Batas Kata</span>
                            <span class="ms-auto">{{ $sites->is_word_limit }} kata</span>
                        </li>
                        <li class="list-group-item d-flex">
                            <span>Kategori</span>
                            <span class="ms-auto">
                                <div id="website-category">
                                    @if(!empty($sites->is_url_category))
                                        <span class="badge bg-primary text-white">{{ $sites->is_url_category }}</span>
                                    @else
                                        <span class="badge bg-dark text-white">Uncategorized</span>
                                    @endif
                                </div>
                            </span>
                        </li>
                    </ul>
                    <div class="row card-footer">
                        <div class="col">
                            <div class="text-center">
                              <span class="fs-2">
                                <strong id="website-da">{{ $sites->is_domain_authority }}</strong>
                              </span>
                              <br>
                              <span>DA</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                              <span class="fs-2">
                                <strong id="website-pa">{{ $sites->is_page_authority }}</strong>
                              </span>
                              <br>
                              <span>PA</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($sites->is_content_included == 1)
                <div class="mt-2">
                    <a href="{{ $sites->is_post_sample }}" noopener noreferrer class="btn btn-outline-secondary w-100"><i class="bx bx-link-alt fs-6 me-2"></i>URL Publisher</a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row mt-4">
      <div class="col-lg-12">
        <h2 class="h3">Ulasan</h2>
      </div>
        @if(!empty($getAllReviews))
        <div class="col-lg-12 mt-4 mb-5" id="review-list">
            <div class="row">
                @foreach($getAllReviews as $review)
                <div class="col-2 bg-white border rounded-4 me-2">
                    <div class="px-2 py-4">
                        <h5 class="h6 fw-bold">{{ $review->user()->first()->name }}</h5>
                        <p class="small text-muted mb-0">{{ $review->review }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
            <div class="col-lg-12">
            <h4 class="h6 text-muted"> Belum ada review </h4>
            </div>
        @endif
    </div>
  </div>
</section>
@include('components.theme.landing.footer')