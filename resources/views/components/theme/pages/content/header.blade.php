<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title -->
    <!-- Required Meta Tag -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="handheldfriendly" content="true" />
    <meta name="MobileOptimized" content="width" />
    <meta content="" name="description" />
    <meta content="" name="keywords" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta https-equiv="Content-Security-Policy"
        content="default-src *; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval'" />
    <title><?= frontend_db('title') . ' - ' . $data['subtitle'] ?></title>
    <!-- Favicon -->
    <link rel="icon" href=" {{ asset('storage/images/' . frontend_db('favicon')) }}" type="image/x-icon" />
    <link rel="stylesheet" href="<?= asset('admin_assets/libs/owl.carousel/dist/assets/owl.carousel.min.css') ?>">
    <link id="themeColors" rel="stylesheet" href="<?= asset('admin_assets/css/style.min.css') ?>" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css?v=<?= time() ?>"
        rel="stylesheet">
    <link href="<?= asset('admin_assets/css/select2.min.css') ?>" rel="stylesheet" />
    <link href="<?= asset('admin_assets/css/custom.min.css') ?>" rel="stylesheet" />
    @stack('css')
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="{{ asset('storage/images/logo_cms.svg') }}" style="width: 150px !important" alt="loader"
            class="lds-ripple img-fluid" />
    </div>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="horizontal" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div class="body-wrapper">
            <div class="container-fluid" style="padding-top: 50px !important;">
