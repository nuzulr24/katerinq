<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <title><?= frontend_db('title') . ' - ' . $data['subtitle'] ?></title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ frontend_db('title') }}" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:site_name" content="{{ frontend_db('title') }}" />
    <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
    <link rel="shortcut icon" href="{{ asset('storage/images/' . frontend_db('favicon')) }}" />

    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ frontend('plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ frontend('css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css"/>
    <script src="{{ frontend('js/app.min.js') }}"></script>
    <style>
        .modal-backdrop {
            background: rgb(151 151 151 / 50%) !important;
            backdrop-filter: blur(4px) !important;
        }

        .modal-backdrop.show {
            opacity: none !important;
        }
    </style>
    @stack('css')
    @livewireStyles
    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->

<!--begin::Body-->
<body id="kt_app_body" data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true"
data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
data-kt-app-sidebar-push-footer="true" class="app-default">
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <!--begin::Page-->
    <div class="app-page  flex-column flex-column-fluid " id="kt_app_page">
        <!--begin::Header-->
        <div id="kt_app_header" class="app-header " data-kt-sticky="true"
            data-kt-sticky-activate="{default: false, lg: true}" data-kt-sticky-name="app-header-sticky"
            data-kt-sticky-offset="{default: false, lg: '300px'}">

            <!--begin::Header container-->
            <div class="app-container  container-fluid d-flex flex-stack " id="kt_app_header_container">
                <!--begin::Sidebar toggle-->
                <div class="d-flex align-items-center d-block d-lg-none ms-n3" title="Show sidebar menu">
                    <div class="btn btn-icon btn-active-color-primary w-35px h-35px me-2"
                        id="kt_app_sidebar_mobile_toggle">
                        <i class="ki-outline ki-abstract-14 fs-2"></i> </div>

                    <!--begin::Logo image-->
                    <a href="{{ url('/') }}">
                        <img alt="Logo" src="{{ asset('storage/images/' . frontend_db('logo')) }}"
                            class="h-30px theme-light-show" />
                        <img alt="Logo" src="{{ asset('storage/images/' . frontend_db('logo')) }}"
                            class="h-30px theme-dark-show" />
                    </a>
                    <!--end::Logo image-->
                </div>
                <!--end::Sidebar toggle-->

                <!--begin::Header wrapper-->
                <div class="d-flex flex-stack flex-lg-row-fluid" id="kt_app_header_wrapper">
                    <!--begin::Page title-->
                    <div class="page-title gap-4 me-3 mb-5 mb-lg-0" data-kt-swapper="1"
                        data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}"
                        data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_wrapper'}">

                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 mb-2">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
                                <a href="{{ url('/') }}" class="text-gray-700 text-hover-primary me-1">
                                    <i class="ki-outline ki-home text-gray-700 fs-6"></i> </a>
                            </li>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <i class="ki-outline ki-right fs-7 text-gray-700 mx-n1"></i> </li>
                            <!--end::Item-->

                            <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
                            {{ ucfirst(segment(1)) }} </li>
                            @if(!empty(segment(2)))
                                <li class="breadcrumb-item">
                                    <i class="ki-outline ki-right fs-7 text-gray-700 mx-n1"></i> </li>
                                <li class="breadcrumb-item text-gray-600 fw-bold lh-1">

                                    {{ ucfirst(segment(2)) }}</li>
                                @if(!empty(segment(3)))
                                    <li class="breadcrumb-item">
                                        <i class="ki-outline ki-right fs-7 text-gray-700 mx-n1"></i> </li>
                                    <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
                                        {{ $data['subtitle'] }} </li>
                                @endif
                            @endif
                        </ul>
                        <!--end::Breadcrumb-->

                        <!--begin::Title-->
                        <h1 class="text-gray-900 fw-bolder m-0 mt-4">
                            {{ $data['subtitle'] }}
                        </h1>
                        <!--end::Title-->
                    </div>
                    <!--end::Page title-->

                    @if(!empty(segment(4)))
                        <a href="{{ site_url('user', 'product/add-to-cart') . '/' . segment(4) }}" class="ms-auto me-2 btn btn-dark">
                            <i class="ki-outline ki-plus fs-3 me-n1"></i>
                            <span class="mb-0">Tambah Keranjang</span>
                        </a>
                    @endif

                    @if(segment(2) === "product" && empty(segment(3)))
                        <a href="{{ site_url('user', 'account/cart') }}" class="btn btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary justify-content-end">
                            <i class="ki-outline ki-purchase fs-3 me-n1"></i>
                            <span class="mb-0">Keranjang</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <!--begin::Wrapper-->
        <div class="app-wrapper  flex-column flex-row-fluid " id="kt_app_wrapper">
            <!--begin::Sidebar-->
            <div id="kt_app_sidebar" class="app-sidebar  flex-column " data-kt-drawer="true"
                data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}"
                data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="start"
                data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

                @if(auth()->user()->level != enum('isAdmin'))
                    <div class="app-sidebar-header d-none d-lg-flex px-6 pt-8 pb-4" id="kt_app_sidebar_header">
                        <button type="button" data-kt-element="selected"
                            class="btn btn-outline btn-custom btn-flex w-100" data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-start" data-kt-menu-offset="0px, -1px">
                            @if(segment(1) === 'user')
                                <span class="d-flex flex-center flex-shrink-0 w-40px me-3">
                                    <img alt="Logo" src="{{ frontend('media/svg/user.svg') }}"
                                        data-kt-element="logo" class="h-30px" />
                                </span>
                                <span class="d-flex flex-column align-items-start flex-grow-1">
                                    <span class="fs-5 fw-bold text-white text-uppercase" data-kt-element="title">
                                        User </span>
                                    <span class="fs-7 fw-bold text-gray-700 lh-sm" data-kt-element="desc">
                                        User Dashboard </span>
                                </span>
                            @else
                                <span class="d-flex flex-center flex-shrink-0 w-40px me-3">
                                    <img alt="Logo" src="{{ frontend('media/svg/market.svg') }}"
                                        data-kt-element="logo" class="h-30px" />
                                </span>
                                <span class="d-flex flex-column align-items-start flex-grow-1">
                                    <span class="fs-5 fw-bold text-white text-uppercase" data-kt-element="title">
                                        Seller </span>
                                    <span class="fs-7 fw-bold text-gray-700 lh-sm" data-kt-element="desc">
                                        Seller Dashboard </span>
                                </span>
                            @endif

                            <span class="d-flex flex-column me-n4">
                                <i class="ki-outline ki-up fs-2 text-gray-700"></i> <i
                                    class="ki-outline ki-down fs-2 text-gray-700"></i> </span>
                        </button>
                    </div>
                @else
                    <div class="app-sidebar-header d-none d-lg-flex px-6 pt-8 pb-4" id="kt_app_sidebar_header">
                        <button type="button" data-kt-element="selected"
                            class="btn btn-outline btn-custom btn-flex w-100" data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-start" data-kt-menu-offset="0px, -1px">
                            <span class="d-flex flex-center flex-shrink-0 w-40px me-3">
                                <img alt="Logo" src="{{ frontend('media/svg/user.svg') }}"
                                    data-kt-element="logo" class="h-30px" />
                            </span>
                            <span class="d-flex flex-column align-items-start flex-grow-1">
                                <span class="fs-5 fw-bold text-white text-uppercase" data-kt-element="title">
                                    Admin </span>
                                <span class="fs-7 fw-bold text-gray-700 lh-sm" data-kt-element="desc">
                                    Admin Dashboard </span>
                            </span>
                        </button>
                    </div>
                @endif
                <x-theme.pages.menu-management/>
                <div class="app-sidebar-footer d-flex flex-stack px-11 pb-10" id="kt_app_sidebar_footer">
                    <!--begin::User menu-->
                    <div class="">
                        <!--begin::Menu wrapper-->
                        <div class="cursor-pointer symbol symbol-circle symbol-40px"
                            data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-overflow="true"
                            data-kt-menu-placement="top-start">
                            <img src="{{ gravatar_team(user()['email']) }}" alt="image" />
                        </div>


                        <!--begin::User account menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                            data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content d-flex align-items-center px-3">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-50px me-5">
                                        <img alt="Logo"
                                            src="{{ gravatar_team(user()['email']) }}" />
                                    </div>
                                    <!--end::Avatar-->

                                    <!--begin::Username-->
                                    <div class="d-flex flex-column">
                                        <div class="fw-bold d-flex align-items-center fs-5">
                                            {{ user()['name'] }}
                                        </div>
                                        <span class="fw-semibold text-muted text-hover-primary fs-7">
                                            {{ user()['email'] }} </span>
                                    </div>
                                    <!--end::Username-->
                                </div>
                            </div>
                            <!--end::Menu item-->

                            <!--begin::Menu separator-->
                            <div class="separator my-2"></div>
                            <!--begin::Menu item-->
                            <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                                <a href="#" class="menu-link px-5">
                                    <span class="menu-title position-relative">
                                        Mode

                                        <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                                            <i class="ki-outline ki-night-day theme-light-show fs-2"></i> <i
                                                class="ki-outline ki-moon theme-dark-show fs-2"></i> </span>
                                    </span>
                                </a>

                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                                    data-kt-menu="true" data-kt-element="theme-mode-menu">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3 my-0">
                                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                            data-kt-value="light">
                                            <span class="menu-icon" data-kt-element="icon">
                                                <i class="ki-outline ki-night-day fs-2"></i> </span>
                                            <span class="menu-title">
                                                Light
                                            </span>
                                        </a>
                                    </div>
                                    <!--end::Menu item-->

                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3 my-0">
                                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                            data-kt-value="dark">
                                            <span class="menu-icon" data-kt-element="icon">
                                                <i class="ki-outline ki-moon fs-2"></i> </span>
                                            <span class="menu-title">
                                                Dark
                                            </span>
                                        </a>
                                    </div>
                                    <!--end::Menu item-->

                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3 my-0">
                                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                            data-kt-value="system">
                                            <span class="menu-icon" data-kt-element="icon">
                                                <i class="ki-outline ki-screen fs-2"></i> </span>
                                            <span class="menu-title">
                                                System
                                            </span>
                                        </a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu-->

                            </div>
                            <div class="menu-item px-5">
                                <a href="{{ route('logout') }}"
                                    class="menu-link px-5">
                                    Keluar
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('logout') }}"
                        class="btn btn-sm btn-outline btn-flex btn-custom px-3">
                        <i class="ki-outline ki-entrance-left fs-2 me-2"></i>
                        Keluar
                    </a>
                </div>
            </div>
            <!--end::Sidebar-->
            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                <div class="d-flex flex-column flex-column-fluid">
                    <div id="kt_app_content" class="app-content flex-column-fluid">
                        <div id="kt_app_content_container" class="app-container container-fluid">
