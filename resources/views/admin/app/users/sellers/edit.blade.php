@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['users.sellers.update', 'id' => $id]]) }}
                    @csrf
                        <div class="row mb-4">
                            <div class="col">
                                <label class="form-label">Pengguna<sup class="text-danger">*</sup></label>
                                <select name="user_id" class="form-control form-select input-seller form-select-solid mt-2 {{ $errors->has('user_id') ? 'is-invalid' : '' }}">
                                    @foreach ($getUserLists as $type)
                                        @php $selected = $type->id == $data['records']['user_id'] ? 'selected' : '' @endphp
                                        <option value="{{ $type->id }}" {{ $selected }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col">
                                <label class="form-label">Nama Usaha<sup class="text-danger">*</sup></label>
                                <input type="text" name="name" value="{{ $data['records']['name'] }}" autocomplete="off" class="form-control form-control-solid mt-2 {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Masukkan Nama Toko">
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Alias<i class="ki-outline ki-information-2 fs-8 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="contoh: s.mhdrizqy"></i></label>
                            <input class="form-control form-control-solid mt-2 {{ $errors->has('alias') ? 'is-invalid' : '' }}" type="text" name="alias" value="{{ $data['records']['alias'] }}" placeholder="cth: s.mhdrizqy">
                            @error('alias') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Status Akun</label>
                            <select name="is_active" class="form-select form-select-solid mt-2 {{ $errors->has('status') ? 'is-invalid' : '' }}">
                                @foreach ([1,2] as $roles)
                                    @php $name = $roles == 1 ? 'Aktif' : 'Tidak Aktif' @endphp
                                    @php $selected = $data['records']['is_active'] == $roles ? 'selected' : '' @endphp
                                    @php $selected_name = $data['records']['is_active'] == $roles ? '(selected)' : '' @endphp
                                    <option value="{{ $roles }}" {{ $selected }}>{{ $name }} {{ $selected_name }}</option>
                                @endforeach
                            </select>
                            @error('is_active') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('users.sellers') }}" class="btn btn-light btn-light">Kembali</a>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script>
    $(document).ready(function () {
        $('.input-seller').select2({
            placeholder: 'Pilih salah satu',
        });
    });
    </script>
@endpush
@include('components.theme.pages.footer')