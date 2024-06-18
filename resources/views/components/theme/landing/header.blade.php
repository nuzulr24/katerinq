<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{ app_info('title') }} - {{ $data['subtitle'] }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ app_info('meta_description') }}">
    <meta name="keywords" content="{{ app_info('meta_keywords') }}">
    <meta name="author" content="{{ app_info('title') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon and Touch Icons -->
    <link class="favicon" rel="apple-touch-icon" sizes="180x180" href="{{ assets_url(app_info('favicon')) }}">
    <link class="favicon" rel="icon" type="image/png" sizes="32x32" href="{{ assets_url(app_info('favicon')) }}">
    <link class="favicon" rel="icon" type="image/png" sizes="16x16" href="{{ assets_url(app_info('favicon')) }}">
    <link rel="manifest" href="{{ pages('third-party/silicon-theme/favicon/site.webmanifest') }}">
    <link class="favicon" rel="mask-icon" href="{{ assets_url(app_info('favicon')) }}" color="#6366f1">
    <link class="favicon" rel="shortcut icon" href="{{ assets_url(app_info('favicon')) }}">
    <meta name="msapplication-TileColor" content="#080032">
    <meta name="msapplication-config" content="{{ pages('third-party/silicon-theme/favicon/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">

    <!--seo meta-->
    <link rel="canonical" href="https://dewabiz.com/" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Postamu.com: {{ app_info('meta_keywords') }}" />
    <meta property="og:description" content="{{ app_info('meta_description') }}" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:updated_time" content="{{ date('Y-m-d H:i:s') }}" />
    <meta property="fb:app_id" content="1046871135361991" />
    <meta property="og:image" content="{{ assets_url(app_info('favicon')) }}" />
    <meta property="og:image:secure_url" content="{{ assets_url(app_info('favicon')) }}" />
    <meta property="og:image:width" content="324" />
    <meta property="og:image:height" content="324" />
    <meta property="og:image:alt" content="hosting murah" />
    <meta property="og:image:type" content="image/png" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Postamu.com: {{ app_info('meta_keywords') }}" />
    <meta name="twitter:description" content="{{ app_info('meta_description') }}" />
    <meta name="twitter:image" content="{{ assets_url(app_info('favicon')) }}" />
    <meta name="twitter:label1" content="Written by" />
    <meta name="twitter:data1" content="postamu" />
    <meta name="twitter:label2" content="Time to read" />
    <meta name="twitter:data2" content="7 minutes" />

    <!-- Vendor Styles -->
    <link rel="stylesheet" media="screen" href="{{ pages('third-party/silicon-theme/vendor/boxicons/css/boxicons.min.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <!-- Main Theme Styles + Bootstrap -->
    <link rel="stylesheet" media="screen" href="{{ pages('third-party/silicon-theme/css/theme.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.5.0-2/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />
    <!-- Page loading styles -->
    <link href="{{ pages('css/main.css') }}" rel="stylesheet">
    <link href="{{ pages('css/common.css') }}" rel="stylesheet">
    @stack('styles')
    @livewireStyles
  </head>
  <body>
    <!-- Page loading spinner -->
    <!--<div class="page-loading active">-->
    <!--  <div class="page-loading-inner">-->
    <!--    <div class="page-spinner"></div><span>Loading...</span>-->
    <!--  </div>-->
    <!--</div>-->

    <main class="page-wrapper">
      <header class="header navbar navbar-expand-lg bg-light navbar-sticky">
        <div class="container px-5">
          <a href="{{ url('/') }}" class="navbar-brand pe-3 brand-logo-section">
            <img src="{{ assets_url(app_info('logo')) }}" alt="Postamu" class="brand-logo" style="width: 150px;">
          </a>
          <div id="navbarNav" class="offcanvas offcanvas-end">
            <div class="offcanvas-header border-bottom">
              <h5 class="offcanvas-title">Menu</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                  <a href="{{ route('marketplace.how-to-sell') }}" class="nav-link">Cara Bergabung</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('marketplace') }}" class="nav-link">Produk</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('blog') }}" class="nav-link">Blog</a>
                </li>
              </ul>
            </div>
            <div class="offcanvas-header border-top" id="login-button-offcanvas">
            </div>
          </div>
          <button type="button" class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          @auth
            @if(user()->level == enum('isAdmin'))
              <a href="{{ app_url('dashboard') }}" class="ms-2 btn btn-primary btn-sm fs-sm rounded d-lg-inline-flex" rel="noopener">
                <i class="bx bx-chevron-left fs-5 lh-1 me-1"></i>
                &nbsp;Kembali ke Laman
              </a>
            @else
              <a href="{{ site_url('user', '/') }}" class="ms-2 btn btn-outline-secondary btn-sm fs-sm rounded d-lg-inline-flex" rel="noopener">
                <i class="bx bx-chevron-left fs-5 lh-1 me-1"></i>
                &nbsp;Kembali ke Laman
              </a>
            @endif
          @else
              <a href="{{ route('login') }}" class="ms-2 btn btn-outline-secondary btn-sm fs-sm rounded d-lg-inline-flex" rel="noopener">
                <i class="bx bx-log-in fs-5 lh-1 me-1"></i>
                &nbsp;Masuk
              </a>
          @endauth
          @auth
            @if(user()->level == enum('isMembers'))
            @canSell
                <div class="nav dropdown d-block order-lg-3 ms-3">
                    <a href="#" class="d-flex nav-link p-0" data-bs-toggle="dropdown">
                      <img src="{{ gravatar_team(user()->email) }}" class="rounded-circle" width="30" alt="Avatar">
                      <div class="d-none d-sm-block ps-2">
                        <div class="fs-xs lh-1 opacity-60">Halo, {{ user()->name }}</div>
                      </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end my-1" style="width: 14rem;">
                      <li>
                        <a href="{{ site_url('user', '/') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-home fs-base opacity-60 me-2"></i>
                          Laman Anda
                        </a>
                      </li>
                      <li>
                        <a href="{{ site_url('seller', '/') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-shopping-bag fs-base opacity-60 me-2"></i>
                          Laman Penjual
                        </a>
                      </li>
                      <li class="dropdown-divider"></li>
                      <li>
                        <a href="{{ site_url('user', 'payment/deposit') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-wallet fs-base opacity-60 me-2"></i>
                          Saldo
                          <span class="ms-auto fs-xs text-muted account-balance">{{ rupiah_changer(user()->balance) }}</span>
                        </a>
                      </li>
                      <li>
                        <a href="{{ site_url('user', 'account') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-bell fs-base opacity-60 me-2"></i>
                          Notifikasi
                          <span class="ms-auto fs-xs text-muted" id="navbar-notification-count">1</span>
                        </a>
                      </li>
                      <li>
                        <a href="{{ site_url('user', 'account') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-group fs-base opacity-60 me-2"></i>
                          Profil
                        </a>
                      </li>
                      <li class="dropdown-divider"></li>
                      <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" id="logout-button">
                          <i class="bx bx-log-out fs-base opacity-60 me-2"></i>
                          Keluar
                        </a>
                      </li>
                    </ul>
                </div>
            @elseCanSell
                <div class="nav dropdown d-block order-lg-3 nav-user">
                    <a href="#" class="d-flex nav-link p-0" data-bs-toggle="dropdown">
                      <img src="{{ gravatar_team('nuzul@progriva.com') }}" class="rounded-circle" width="48" alt="Avatar">
                      <div class="d-none d-sm-block ps-2">
                        <div class="fs-xs lh-1 opacity-60">Halo, {{ user()->name }}</div>
                        <div class="fs-sm dropdown-toggle"></div>
                      </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end my-1" style="width: 14rem;">
                      <li>
                        <a href="{{ site_url('user', '/') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-shopping-bag fs-base opacity-60 me-2"></i>
                          Laman Anda
                        </a>
                      </li>
                      <li>
                        <a href="{{ site_url('seller', '/') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-shopping-bag fs-base opacity-60 me-2"></i>
                          Laman Penjual
                        </a>
                      </li>
                      <li class="dropdown-divider"></li>
                      <li>
                        <a href="{{ site_url('user', '/') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-wallet fs-base opacity-60 me-2"></i>
                          Saldo
                          <span class="ms-auto fs-xs text-muted">{{ 'Rp. ' . number_format(user()->balance, 0, ',', '.') }}</span>
                        </a>
                      </li>
                      <li>
                        <a href="{{ site_url('user', 'account') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-bell fs-base opacity-60 me-2"></i>
                          Notifikasi
                          <span class="ms-auto fs-xs text-muted" id="navbar-notification-count">1</span>
                        </a>
                      </li>
                      <li>
                        <a href="{{ site_url('user', 'account') }}" class="dropdown-item d-flex align-items-center">
                          <i class="bx bx-group fs-base opacity-60 me-2"></i>
                          Profil
                        </a>
                      </li>
                      <li class="dropdown-divider"></li>
                      <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" id="logout-button">
                          <i class="bx bx-log-out fs-base opacity-60 me-2"></i>
                          Keluar
                        </a>
                      </li>
                    </ul>
                </div>
            @endCanSell
            @endif
          @endauth
        </div>
      </header>
