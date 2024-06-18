<div class="app-sidebar-navs flex-column-fluid mx-2 py-6" id="kt_app_sidebar_navs">
    <div data-simplebar id="kt_app_sidebar_navs_wrappers" class="my-2" data-kt-scroll="true"
        data-kt-scroll-activate="true" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_header, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_navs" data-kt-scroll-offset="5px">

        {{-- Basic::Quick Links --}}
        @if(user()['level'] == enum('isAdmin'))
            <div id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false"
                class="menu menu-column menu-rounded menu-sub-indention menu-active-bg">
                <div class="menu-item">
                    <a href="{{ app_url('dashboard') }}" class="menu-link">
                        <span class="menu-icon"><i class="ki-outline ki-home fs-1"></i></span>
                        <span class="menu-title">Utama</span>
                    </a>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link"><span class="menu-icon"><i
                        class="ki-outline ki-book fs-2"></i></span><span class="menu-title">Konten</span><span
                    class="menu-arrow"></span></span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ active_page('content') }}" href="{{ app_url('content') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Semua</span></a>
                            <a class="menu-link {{ active_page('content.create') }}" href="{{ app_url('content/create') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Tambah Baru</span></a>
                            <a class="menu-link {{ active_page('content.categories') }}" href="{{ app_url('content/categories') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Kategori</span></a>
                            <a class="menu-link {{ active_page('content.tag') }}" href="{{ app_url('content/tag') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Tag</span></a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link"><span class="menu-icon"><i
                        class="ki-outline ki-lots-shopping fs-2"></i></span><span class="menu-title">Pesanan</span><span
                    class="menu-arrow"></span></span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ active_page('services') }}" href="{{ app_url('orders') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Semua</span></a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link"><span class="menu-icon"><i
                        class="ki-outline ki-fasten fs-2"></i></span><span class="menu-title">Produk</span><span
                    class="menu-arrow"></span></span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ active_page('services') }}" href="{{ app_url('product') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Semua</span></a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link"><span class="menu-icon"><i
                        class="ki-outline ki-file-up fs-2"></i></span><span class="menu-title">Pembayaran</span><span
                    class="menu-arrow"></span></span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ active_page('services') }}" href="{{ app_url('billing/withdrawal') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Withdrawal</span></a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link"><span class="menu-icon"><i
                        class="ki-outline ki-note fs-2"></i></span><span class="menu-title">Halaman</span><span
                    class="menu-arrow"></span></span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ active_page('pages') }}" href="{{ app_url('pages') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Semua</span></a>
                            <a class="menu-link {{ active_page('pages.create') }}" href="{{ app_url('pages/create') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Tambah baru</span></a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link"><span class="menu-icon"><i
                        class="ki-outline ki-chart-line fs-2"></i></span><span class="menu-title">Laporan</span><span
                    class="menu-arrow"></span></span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ active_page('pages') }}" href="{{ app_url('report/cashflow') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Keuangan & Transaksi</span></a>
                            <a class="menu-link {{ active_page('pages.create') }}" href="{{ app_url('report/statistics') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Statistik</span></a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link"><span class="menu-icon"><i
                        class="ki-outline ki-people fs-2"></i></span><span class="menu-title">Pengguna</span><span
                    class="menu-arrow"></span></span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ active_page('users') }}" href="{{ app_url('users') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Semua</span></a>
                            <a class="menu-link {{ active_page('users') }}" href="{{ app_url('users/sellers') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Penjual</span></a>
                            <a class="menu-link {{ active_page('users.create') }}" href="{{ app_url('users/create') }}"><span
                            class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                            class="menu-title">Tambah baru</span></a>
                        </div>
                    </div>
                </div>
                <div class="menu-item">
                    <a href="{{ app_url('settings') }}" class="menu-link">
                        <span class="menu-icon"><i class="ki-outline ki-setting-3 fs-1"></i></span>
                        <span class="menu-title">Pengaturan</span>
                    </a>
                </div>
            </div>
        @endif
        @if(user()['level'] == 2)
        <div id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false"
            class="menu menu-column menu-rounded menu-sub-indention menu-active-bg">
            <div class="menu-item">
                <a href="{{ site_url('seller','/') }}" class="menu-link">
                    <span class="menu-icon"><i class="ki-outline ki-home fs-1"></i></span>
                    <span class="menu-title">Utama</span>
                </a>
            </div>
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                <span class="menu-link"><span class="menu-icon"><i
                    class="ki-outline ki-fasten fs-2"></i></span><span class="menu-title">Produk</span><span
                class="menu-arrow"></span></span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link {{ active_page('content') }}" href="{{ site_url('seller', 'product') }}"><span
                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                        class="menu-title">Semua</span></a>
                        <a class="menu-link {{ active_page('content.create') }}" href="{{ site_url('seller', 'product/create') }}"><span
                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                        class="menu-title">Daftar</span></a>
                    </div>
                </div>
            </div>
            <div class="menu-item">
                <a href="{{ site_url('seller', 'transaction') }}" class="menu-link">
                    <span class="menu-icon"><i class="ki-outline ki-purchase fs-1"></i></span>
                    <span class="menu-title">Pesanan</span>
                </a>
            </div>
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                <span class="menu-link"><span class="menu-icon"><i
                    class="ki-outline ki-wallet fs-2"></i></span><span class="menu-title">Pembayaran</span><span
                class="menu-arrow"></span></span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link {{ active_page('services') }}" href="{{ site_url('seller', 'account/withdrawal') }}"><span
                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                        class="menu-title">Penarikan</span></a>
                        <a class="menu-link {{ active_page('services') }}" href="{{ site_url('seller', 'account/rekening') }}"><span
                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                        class="menu-title">Rekening</span></a>
                        <a class="menu-link {{ active_page('services.create') }}" href="{{ site_url('seller', 'account/rekening/create') }}"><span
                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                        class="menu-title">Tambah Rekening</span></a>
                    </div>
                </div>
            </div>
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                <span class="menu-link"><span class="menu-icon"><i
                    class="ki-outline ki-chart-line fs-2"></i></span><span class="menu-title">Laporan & Statistik</span><span
                class="menu-arrow"></span></span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link {{ active_page('users') }}" href="{{ site_url('seller', 'report') }}"><span
                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                        class="menu-title">Keuangan & Transaksi</span></a>
                        <a class="menu-link {{ active_page('users.create') }}" href="{{ site_url('seller', 'report/statistic') }}"><span
                        class="menu-bullet"><span class="bullet bullet-dot"></span></span><span
                        class="menu-title">Statistik</span></a>
                    </div>
                </div>
            </div>
            <div class="menu-item">
                <a href="{{ site_url('seller', 'account') }}" class="menu-link">
                    <span class="menu-icon"><i class="ki-outline ki-setting-3 fs-1"></i></span>
                    <span class="menu-title">Pengaturan Bisnis</span>
                </a>
            </div>
        </div>
        @elseif(user()['level'] == 4)
        <div id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false"
            class="menu menu-column menu-rounded menu-sub-indention menu-active-bg">
            <div class="menu-item">
                <a href="{{ site_url('user','/') }}" class="menu-link">
                    <span class="menu-icon"><i class="ki-outline ki-home fs-1"></i></span>
                    <span class="menu-title">Utama</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ site_url('user', 'product') }}" class="menu-link">
                    <span class="menu-icon"><i class="ki-outline ki-fasten fs-1"></i></span>
                    <span class="menu-title">Produk</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ site_url('user', 'orders') }}" class="menu-link">
                    <span class="menu-icon"><i class="ki-outline ki-purchase fs-1"></i></span>
                    <span class="menu-title">Pesanan</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ site_url('user', 'account') }}" class="menu-link">
                    <span class="menu-icon"><i class="ki-outline ki-setting-3 fs-1"></i></span>
                    <span class="menu-title">Pengaturan Akun</span>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
