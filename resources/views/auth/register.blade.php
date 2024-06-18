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
    <div class="d-flex flex-column flex-column-fluid flex-lg-row justify-content-center">
        {{-- begin::body sign-in --}}
        <div
            class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
            <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">
                <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">

                    <!--begin::Form-->
                    <form class="form w-100" method="POST" novalidate="novalidate" id="kt_sign_in_form" action="{{ route('storeRegister') }}">
                        @csrf
                        <div class="text-center mb-11">
                            <h1 class="text-dark fw-bolder mb-3">
                                Daftar
                            </h1>

                            <div class="text-gray-500 fw-semibold fs-6">
                                Mendaftar untuk dapat menggunakan akses
                            </div>
                        </div>

                        {{-- begin::forms sign-in --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Alamat Email<sup class="text-danger">*</sup></label>
                                <input type="email" name="email" value="{{ old('email') }}" autocomplete="off" class="form-control form-control-solid mt-2 {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="Masukkan alamat email">
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap<sup class="text-danger">*</sup></label>
                                <input type="text" name="name" value="{{ old('name') }}" autocomplete="off" class="form-control form-control-solid mt-2 {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Masukkan nama lengkap">
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Bergabung sebagai<sup class="text-danger">*</sup></label>
                            <select class="form-select form-select-solid" name="level">
                                <option value="">Pilih</option>
                                <option value="2" {{ old('level') == 2 ? 'selected' : '' }}>Vendor Party Planner</option>
                                <option value="4" {{ old('level') == 4 ? 'selected' : '' }}>Klien</option>
                            </select>
                            @error('level') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group mb-4" data-kt-password-meter="true">
                            <label class="form-label">Password<sup class="text-danger">*</sup></label>
                            <div class="position-relative mb-3">
                                <input class="form-control form-control-lg form-control-solid mt-2 {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                    type="password" value="{{ old('password') }}" placeholder="Masukkan kata sandi" name="password" autocomplete="off" />

                                <!--begin::Visibility toggle-->
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                    data-kt-password-meter-control="visibility">
                                        <i class="ki-duotone ki-eye-slash fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        <i class="ki-duotone ki-eye d-none fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </span>
                                <!--end::Visibility toggle-->
                            </div>

                            <!--begin::Highlight meter-->
                            <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                            </div>

                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            <div class="text-muted">
                                Min. kata sandi 8 karakter
                            </div>
                        </div>

                        <!--begin::Submit button-->
                        <div class="d-grid mb-10">
                            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                <span class="indicator-label">
                                    Daftar</span>
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
                            <a href="{{ route('login') }}" class="link-primary">
                                Masuk sekarang
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
