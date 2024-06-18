    </main>

    <!-- Footer -->
    <footer class="footer dark-mode bg-dark pt-5 pb-4 pb-lg-5 px-5">
      <div class="container pt-lg-4">
        <div class="row pb-5">
          <div class="col-lg-4 col-md-6">
            <div class="navbar-brand text-dark p-0 me-0 mb-3 mb-lg-4 brand-logo-section">
              <img src="{{ assets_url(app_info('logo')) }}" width="170" alt="Postamu" class="brand-logo">
            </div>
            <div class="row  mb-2" id="footer-contact">
              <div class="col-12">
                <h6>Kontak Kami</h6>
              </div>
            </div>
            <div class="row mb-2" id="footer-email-section">
              <div class="col-1">
                <i class="bx bx-envelope fs-5 lh-1 me-1"></i>
              </div>
              <div class="col-11">
                <span class="fw-medium" id="footer-email">{{ app_info('email') }}</span>
              </div>
            </div>
            <div class="row mb-2" id="footer-phone-section">
              <div class="col-1">
                <i class="bx bx-phone fs-5 lh-1 me-1"></i>
              </div>
              <div class="col-11">
                <span class="fw-medium" id="footer-phone">{{ app_info('phone') }}</span>
              </div>
            </div>
            <div class="row mb-2" id="footer-address-section">
              <div class="col-1">
                <i class="bx bx-home fs-5 lh-1 me-1"></i>
              </div>
              <div class="col-11" id="footer-address">
                <p class="fs-sm text-light opacity-70">{{ app_info('address') }}</p>
              </div>
            </div>
          </div>
          <div class="col-xl-6 col-lg-7 col-md-5 offset-xl-2 offset-md-1 pt-4 pt-md-1 pt-lg-0">
            <div id="footer-links" class="row">
              <div class="col-lg-4 col-4">
                <h6 class="mb-2">
                  Halaman
                </h6>
                <div id="useful-links" class="d-lg-block" data-bs-parent="#footer-links">
                  <ul class="nav flex-column pb-lg-1 mb-lg-3">
                    <li class="nav-item"><a href="{{ url('/') }}" class="nav-link d-inline-block px-0 pt-1 pb-2">Beranda</a></li>
                    <li class="nav-item"><a href="{{ route('marketplace.how-to-sell') }}" class="nav-link d-inline-block px-0 pt-1 pb-2">Gabung Vendor</a></li>
                    <li class="nav-item"><a href="{{ route('marketplace') }}" class="nav-link d-inline-block px-0 pt-1 pb-2">Marketplace</a></li>
                    <li class="nav-item"><a href="{{ route('blog') }}" class="nav-link d-inline-block px-0 pt-1 pb-2">Blog</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-lg-3 col-4">
                <h6 class="mb-2">
                  Informasi
                </h6>
                <div id="useful-links" class="d-lg-block" data-bs-parent="#footer-links">
                  <ul class="nav flex-column mb-2 mb-lg-0" id="footer-info">
                    <li class="nav-item"><a href="{{ route('about') }}" class="nav-link d-inline-block px-0 pt-1 pb-2">Tentang Kami</a></li>
                    @foreach(\App\Models\Pages::where('is_status', 1)->limit(5)->get() as $page)
                        <li class="nav-item"><a href="{{ route('info-page', $page->slug) }}" class="nav-link d-inline-block px-0 pt-1 pb-2">{{ $page->title }}</a></li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <p class="nav d-block fs-xs text-center text-md-start pb-2 pb-lg-0 mb-0">
          <span class="text-light opacity-50">&copy; Copyright {{ app_info('title') }} {{ date('Y') }}. All rights reserved. </span>
        </p>
      </div>
    </footer>

    <div class="modal fade" id="generalModal" tabindex="-1" aria-labelledby="generalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered customSizing">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generalModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body result">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xs modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Apa anda yakin untuk memproses ini ?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Jika anda yakin memproses ini, silahkan klik tombol lanjutkan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <a href="" class="btn btn-danger link">Lanjutkan</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to top button -->
    <a href="#top" class="btn-scroll-top" data-scroll>
      <span class="btn-scroll-top-tooltip text-muted fs-sm me-2">Top</span>
      <i class="btn-scroll-top-icon bx bx-chevron-up"></i>
    </a>

    <!-- Vendor Scripts -->
    <script src="{{ pages('third-party/silicon-theme/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ pages('third-party/silicon-theme/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js') }}"></script>
    <script src="{{ pages('third-party/silicon-theme/vendor/jarallax/dist/jarallax.min.js') }}"></script>
    <script src="{{ pages('third-party/silicon-theme/vendor/parallax-js/dist/parallax.min.js') }}"></script>
    <script src="{{ pages('third-party/silicon-theme/vendor/rellax/rellax.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
    <!-- Main Theme Script -->
    <script src="{{ pages('third-party/silicon-theme/js/theme.min.js') }}"></script>
    <!-- Vendor CDN JS Files -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.5.0-2/js/all.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://preview.keenthemes.com/html/metronic/docs/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-star-rating@4.1.2/js/star-rating.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-star-rating@4.1.2/themes/krajee-svg/theme.js"></script>
    <script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    @livewireScripts
    <?= swal_response() ?>
    <script>
        @stack('scripts')
        $('.typed-cursor').addClass('d-none');
        AOS.init();
        let mode = window.localStorage.getItem('mode'),
        root = document.getElementsByTagName('html')[0];
        if (mode !== null && mode === 'dark') {
            root.classList.add('dark-mode');
        } else {
            root.classList.remove('dark-mode');
        }
        (function () {
            window.onload = function () {
              const preloader = document.querySelector('.page-loading');
              preloader.classList.remove('active');
              setTimeout(function () {
                preloader.remove();
              }, 1000);
            };
        })();
    </script>
  </body>
</html>
