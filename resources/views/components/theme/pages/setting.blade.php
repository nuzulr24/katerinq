<div class="col-md-12">
    <div class="card">
        <div class="card-body pt-0 pb-0">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                <!--begin::Nav item-->
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6 @if(empty(segment(3))) active @endif" href="{{ app_url('settings') }}">
                        Umum
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6 @if(!empty(segment(3)) && segment(3) === 'media') active @endif" href="{{ app_url('settings/media') }}">
                        Media
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6 @if(!empty(segment(3)) && segment(3) === 'seo') active @endif" href="{{ app_url('settings/seo') }}">
                        SEO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6 @if(!empty(segment(3)) && segment(3) === 'payment') active @endif" href="{{ app_url('settings/payment') }}">
                        Pembayaran
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
