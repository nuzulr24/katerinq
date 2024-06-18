@include('components.theme.landing.header')
<section class="dark-mode bg-dark position-relative pt-5 pb-4 py-md-5">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-secondary"></div>
    <div class="container position-relative zindex-3 py-lg-4 pt-md-2 py-xl-5 mb-lg-4">

    <div class="row justify-content-center text-center pb-4 mb-2">
        <div class="col-xl-6 pt-2">
          <h2 class="h1 mb-4">Bergabung sebagai Vendor {{ app_info('title') }} &#128196;</h2>
          <p class="fs-lg mb-3">Pahami prosedur cara kerja dengan mengikuti langkah-langkah berikut</p>
        </div>
    </div>
    <div class="row mt-3">
      <div class="col py-4 my-2 my-sm-3">
        <div class="card card-hover h-100 border-0 shadow-sm text-decoration-none pt-5 px-sm-3 px-md-0 px-lg-3 pb-sm-3 pb-md-0 pb-lg-3 me-xl-2">
          <div class="card-body pt-3">
            <div class="d-inline-block bg-primary shadow-primary rounded-3 position-absolute top-0 translate-middle-y p-3">
              <i class="bx bx-globe text-white fs-1"></i>
            </div>
            <h2 class="h4 d-inline-flex align-items-center">
              Daftar sebagai Vendor
            </h2>
            <p class="fs-sm text-body mb-0">
              Daftar gratis, dan mulai proses pendaftaran vendor.
            </p>
          </div>
        </div>
      </div>
      <div class="col py-4 my-2 my-sm-3">
        <div class="card card-hover h-100 border-0 shadow-sm text-decoration-none pt-5 px-sm-3 px-md-0 px-lg-3 pb-sm-3 pb-md-0 pb-lg-3 me-xl-2">
          <div class="card-body pt-3">
            <div class="d-inline-block bg-primary shadow-primary rounded-3 position-absolute top-0 translate-middle-y p-3">
              <i class="bx bx-rocket text-white fs-1"></i>
            </div>
            <h2 class="h4 d-inline-flex align-items-center">
              Masukkan Produk yang Anda Tawarkan
            </h2>
            <p class="fs-sm text-body mb-0">
              Anda dapat dengan bebas memasukkan semua produk yang Anda tawarkan
            </p>
          </div>
        </div>
      </div>
      <div class="col py-4 my-2 my-sm-3">
        <div class="card card-hover h-100 border-0 shadow-sm text-decoration-none pt-5 px-sm-3 px-md-0 px-lg-3 pb-sm-3 pb-md-0 pb-lg-3 me-xl-2">
          <div class="card-body pt-3">
            <div class="d-inline-block bg-primary shadow-primary rounded-3 position-absolute top-0 translate-middle-y p-3">
              <i class="bx bx-dollar-circle text-white fs-1"></i>
            </div>
            <h2 class="h4 d-inline-flex align-items-center">
              Dapatkan Bayaran
            </h2>
            <p class="fs-sm text-body mb-0">
              Setelah Anda menyelesaikan pesanan pembeli, Dapatkan bayaran untuk pekerjaan Anda.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="position-relative pt-5 pb-4 py-md-5">
        <div class="position-absolute top-0 start-0 w-100 h-100"></div>
        <div class="container position-relative zindex-3 py-lg-4 pt-md-2 py-xl-5 mb-lg-4">

        <div class="row justify-content-center text-center pb-4 mb-2">
            <div class="col-xl-6 pt-2">
              <h2 class="h1 mb-4">Anda siap memulai? &#128064;</h2>
              <p class="fs-lg text-muted mb-0">Bergabunglah dengan {{ app_info('title') }} untuk memulai bisnis sebagai vendor</p>
            </div>
        </div>

        <div class="row mt-3 justify-content-center">
            <div class="col-6">
                @auth
                    @if(user()->level == 4)
                        <div class="alert alert-primary">
                            Anda telah masuk pada aplikasi, tekan kembali jika ingin kembali ke laman user <a href="{{ site_url('user', '/') }}">klik disini</a>
                        </div>
                    @elseif(user()->level == 2)
                        <div class="alert alert-primary">
                            Anda telah masuk pada aplikasi, tekan kembali jika ingin kembali ke laman admin <a href="{{ site_url('seller', '/') }}">klik disini</a>
                        </div>
                    @elseif(user()->level == enum('isAdmin'))
                        <div class="alert alert-primary">
                            Anda telah masuk pada aplikasi, tekan kembali jika ingin kembali ke laman admin <a href="{{ app_url('/') }}">klik disini</a>
                        </div>
                    @endif
                @else
                    <form id="registration-form" method="POST" action="{{ route('storeRegister') }}" class="php-email-form mt-4">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="first_name text-dark">Nama Lengkap<sup clas="ms-2 " style="color: red">*</sup></label>
                            <input type="text" name="name" class="form-control mt-2" id="name" value="{{ old('name') }}" autocomplete="off" placeholder="Ketik nama lengkap.." required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="first_name text-dark">Alamat Email<sup clas="ms-2 " style="color: red">*</sup></label>
                            <input type="email" name="email" class="form-control mt-2" id="email" value="{{ old('email') }}" autocomplete="off" placeholder="Ketik alamat email.." required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="first_name text-dark">Alamat Email<sup clas="ms-2 " style="color: red">*</sup></label>
                            <input type="password" name="password" class="form-control mt-2" id="email" value="{{ old('password') }}" autocomplete="off" placeholder="Ketik kata sandi.." required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Bergabung sebagai<sup class="text-danger">*</sup></label>
                            <select class="form-control mt-2" name="level">
                                <option value="">Pilih</option>
                                <option value="2" {{ old('level') == 2 ? 'selected' : '' }}>Vendor Party Planner</option>
                                <option value="4" {{ old('level') == 4 ? 'selected' : '' }}>Klien</option>
                            </select>
                            @error('level') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="text-center mt-2 pt-2 pt-md-3 pt-lg-4"><button type="submit" id="registration-button" class="btn btn-primary shadow-primary btn-lg">Daftar sekarang</button></div>
                    </form>
                @endauth
            </div>
        </div>
    </div>
  </section>
@include('components.theme.landing.footer')
