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
@push('scripts') <script src="{{ frontend('js/app.min.js') }}"></script> @endpush
<x-theme.auth.authentication__-header :data="$data" />
<div class="d-flex flex-column flex-root" id="kt_app_root">
    {{-- begin::authentication sign-in --}}
    <div class="d-flex flex-column flex-column-fluid flex-lg-row">
        <div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
            <div class="d-flex flex-center flex-lg-start flex-column">
                <a href="{{ url('/') }}" class="mb-7">
                    <img alt="Logo" src="{{ assets_url('images/logo-light.svg') }}" />
                </a>
                <h2 class="text-white fw-normal m-0 mb-3">
                    Single Submission Online (SSO) Platform POSTAMU
                </h2>
                <p class="text-muted fw-normal mb-0">
                    Platform Jual-Beli Website dan Domain Terpercaya dengan bermacam-macam untuk keperluan kreator maupun publisher.
                </p>
            </div>
        </div>
        {{-- begin::body sign-in --}}
        <div
            class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
            <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">
                <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">

                    <!--begin::Form-->
                    <form class="form w-100" method="POST" novalidate="novalidate" id="kt_sign_in_form" action="{{ route('resetPassword') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="text-center mb-11">
                            <h1 class="text-dark fw-bolder mb-3">
                                Atur Ulang Sandi
                            </h1>

                            <div class="text-gray-500 fw-semibold fs-6">
                                Lengkapi formulir dibawah ini untuk memperbarui kata sandi.
                            </div>
                        </div>

                        {{-- begin::forms sign-in --}}
                        <div class="form-group mb-4" data-kt-password-meter="true">
                            <label class="form-label">Password<sup class="text-danger">*</sup></label>
                            <div class="position-relative mb-3">
                                <input class="form-control form-control-lg form-control-solid mt-2 {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                    type="password" value="{{ old('password') }}" placeholder="Masukkan kata sandi" name="password" autocomplete="off" />
                            </div>

                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            <div class="text-muted">
                                Min. kata sandi 8 karakter
                            </div>
                        </div>

                        <div class="form-group mb-4" data-kt-password-meter="true">
                            <label class="form-label">Ulang Password<sup class="text-danger">*</sup></label>
                            <div class="position-relative mb-3">
                                <input class="form-control form-control-lg form-control-solid mt-2 {{ $errors->has('retype_password') ? 'is-invalid' : '' }}"
                                    type="password" value="{{ old('retype_password') }}" placeholder="Masukkan ulang kata sandi" name="retype_password" autocomplete="off" />
                            </div>

                            @error('retype_password') <small class="text-danger">{{ $message }}</small> @enderror
                            <div class="text-muted">
                                Min. kata sandi 8 karakter
                            </div>
                        </div>

                        <!--begin::Submit button-->
                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                <span class="indicator-label">
                                    Perbarui</span>
                                <span class="indicator-progress">
                                    Please wait... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Submit button-->

                        <!--begin::Sign up-->
                        <div class="text-gray-500 text-center fw-semibold fs-6">
                            Sudah memiliki akun?
                            <a href="{{ url('/') }}" class="link-primary">
                                Masuk
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
