<div class="col-md-12">
    <div class="card">
        <div class="card-body pt-0 pb-0">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                @if(user()['level'] == enum('isMembers'))
                    @if(segment(1) === "seller")
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 me-6 @if(empty(segment(3))) active @endif" href="{{ site_url('seller', 'account') }}">
                                Umum
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 me-6 @if(empty(segment(3))) active @endif" href="{{ site_url('user', 'account') }}">
                                Umum
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 me-6 @if(!empty(segment(3)) && segment(3) === 'activity') active @endif" href="{{ site_url('user', 'account/activity') }}">
                                Riwayat Aktifitas
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>
</div>
