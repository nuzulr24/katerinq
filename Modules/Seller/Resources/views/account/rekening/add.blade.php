@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ site_url('seller', 'account/rekening/store') }}">
                    @csrf
                        <div class="form-group mb-4">
                            <label class="form-label">Atas Nama<sup class="text-danger">*</sup></label>
                            <input type="text" name="name" value="{{ old('name') }}" autocomplete="off" class="form-control form-control-solid mt-2 {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Masukkan Atas Nama">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Nomer Rekening<sup class="text-danger">*</sup></label>
                            <input type="number" name="account_number" value="{{ old('account_number') }}" autocomplete="off" class="form-control form-control-solid mt-2 {{ $errors->has('account_number') ? 'is-invalid' : '' }}" placeholder="Masukkan Nomor Rekening">
                            @error('account_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Daftar Bank<sup class="text-danger">*</sup></label>
                            <select name="rid" class="form-select form-select-solid input-bank mt-2 {{ $errors->has('rid') ? 'is-invalid' : '' }}">
                                @foreach ($listAllBank as $bank)
                                    @if (strlen($bank->kodebank) == 1)
                                        @php $code = '00' . $bank->kodebank @endphp
                                    @elseif(strlen($bank->kodebank) == 2)
                                        @php $code = '0' . $bank->kodebank @endphp
                                    @else
                                        @php $code = $bank->kodebank @endphp
                                    @endif
                                    <option value="{{ $bank->id }}">Kode Bank: {{ $code }} / {{ $bank->nama }}</option>
                                @endforeach
                            </select>
                            @error('rid') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('rekening') }}" class="btn btn-light btn-light">Kembali</a>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script src="https://preview.keenthemes.com/html/metronic/docs/assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js"></script>
    <script>
    $(document).ready(function () {
        $('.input-bank').select2({
            placeholder: 'Pilih salah satu',
            dropdownParent: $('.input-bank').parent()
        });
    });
    </script>
@endpush
@include('components.theme.pages.footer')
