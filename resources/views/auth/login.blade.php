@push('css')
<style>
    body {
        background-image: url('{{ frontend("media/auth/bg4.jpg") }}');
    }

    [data-bs-theme="dark"] body {
        background-image: url('{{ frontend("media/auth/bg4-dark.jpg") }}');
    }
</style>
@endpush
@push('scripts') <script src="{{ frontend('js/custom/page.js') }}"></script> @endpush
<x-theme.auth.authentication__-header :data="$data" />
<div class="d-flex flex-column flex-root" id="kt_app_root">
    {{-- begin::authentication sign-in --}}
    <div class="d-flex flex-column flex-column-fluid flex-lg-row justify-content-center">
        <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
            <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">
                <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">

                    <!--begin::Form-->
                    <form class="form w-100" method="POST" novalidate="novalidate" id="kt_sign_in_form" action="{{ route('proses_login') }}">
                        @csrf
                        <div class="text-center mb-11">
                            <h1 class="text-dark fw-bolder mb-3">
                                Masuk
                            </h1>

                            <div class="text-gray-500 fw-semibold fs-6">
                                Masuk menggunakan kredensial yang anda miliki
                            </div>
                        </div>

                        {{-- begin::forms sign-in --}}
                        <div class="fv-row mb-8">
                            <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" autocomplete="off"
                                class="form-control bg-transparent {{ $errors->has('email') ? 'is-invalid' : '' }}" />
                            @if($errors->has('email'))<span class="small text-danger mt-2">{{ $errors->first('email') }}</span>@endif
                        </div>
                        <div class="fv-row mb-3">
                            <input type="password" placeholder="Password" name="password" value="{{ old('password') }}" autocomplete="off"
                                class="form-control bg-transparent {{ $errors->has('password') ? 'is-invalid' : '' }}" />
                            @if($errors->has('password'))<span class="small text-danger mt-2">{{ $errors->first('password') }}</span>@endif
                        </div>

                        <!--begin::Submit button-->
                        <div class="d-grid mb-10">
                            <button type="submit" class="btn btn-primary mt-3">
                            Masuk
                            </button>
                        </div>
                        <!--end::Submit button-->

                        <!--begin::Sign up-->
                        <div class="text-gray-500 text-center fw-semibold fs-6">
                            Belum memiliki akun?
                            <a href="{{ route('register') }}" class="link-primary">
                                Daftar segera
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    {{-- end::authentication sign-in --}}
</div>
<x-theme.auth.authentication__-footer />
