<!-- Page content -->
<section class="container mt-4 mb-2 mb-md-4 mb-lg-5 pt-lg-2 pb-5">
  <div class="row mb-2 col-12">
    <h4 class="d-flex align-items-center">
      Berita & Informasi
    </h4>
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
  <!-- Blog list + Sidebar -->
  <div class="row">
    <div class="col-xl-12 col-lg-8">
      <div class="row" id="blog-list">
        @foreach($sites as $items)
            <article class="card border-0 bg-transparent me-xl-5 mb-4">
              <div class="row g-0">
                <div class="col-sm-5 position-relative bg-position-center bg-repeat-0 bg-size-cover rounded-3" style="background-image: url('{{ !empty($items->is_thumbnail) ? assets_url($items->is_thumbnail) : 'https://wallpapers.com/images/featured/blank-white-7sn5o1woonmklx1h.jpg' }}'); min-height: 15rem;">
                  <a href="{{ route('blog.detail', $items->slug) }}" class="position-absolute top-0 start-0 w-100 h-100" aria-label="Read more"></a>
                </div>
                <div class="col-sm-7">
                  <div class="card-body px-0 pt-sm-0 ps-sm-4 pb-0 pb-sm-4">
                    <span class="badge fs-sm text-white bg-info shadow-info text-decoration-none mb-3">{{ $items->category->first()->name }}</span>
                    <h3 class="h4">
                      <a href="{{ route('blog.detail', $items->slug) }}">{{ $items->title }}</a>
                    </h3>
                    <p class="mb-4">{!! substr($items->description, 0, 200) . '... lihat selengkapnya' !!}</p>
                    <div class="d-flex align-items-center text-muted">
                      <div class="fs-sm pe-3 me-3">{{ date_formatting($items->is_created, 'timeago') }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </article>
        @endforeach

        <ul class="pagination pagination-outline justify-content-center align-items-center mt-5">
            @if ($sites->onFirstPage())
                <li class="page-item previous disabled"><span class="page-link"><i class="previous"></i></span></li>
            @else
                <li class="page-item"><a href="{{ $sites->previousPageUrl() }}" class="page-link"><i class="previous"></i></a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item"><span class="page-link">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @php
                            $pattern = '/\/livewire\/message\/([A-Za-z0-9_\.]+)\?/';
                            $replacement = '/user/sites?';
                            $urls = preg_replace($pattern, $replacement, $url);
                        @endphp
                        @if ($page == $sites->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a href="{{ $urls }}" class="page-link">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($sites->hasMorePages())
                <li class="page-item"><a href="{{ $sites->nextPageUrl() }}" class="page-link"><i class="next"></i></a></li>
            @else
                <li class="page-item next disabled"><span class="page-link"><i class="next"></i></span></li>
            @endif
        </ul>
      </div>
    </div>
  </div>
</section>
