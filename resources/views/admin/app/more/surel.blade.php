@include('components.theme.pages.header')
<section>
    <div class="row">
        @php
            $url_segment = segment(3);
            if(empty($url_segment)) {
                $url_segment = '';
            }
        @endphp
        <x-theme.pages.setting :activeSetting="$url_segment" />
        <div class="col-md-12 mt-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Surel</h3>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'settings.store.surel']) !!}
                    @csrf

                    <div class="row mb-4">
                        <div class="col-6 form-group">
                            <label class="form-label">Alamat Email<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="username" class="form-control form-control-solid mt-2" value="{{ app_smtp_info()->username }}" placeholder="Alamat Email">
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label">Kata Sandi<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="password" class="form-control form-control-solid mt-2" value="{{ app_smtp_info()->password }}" placeholder="Kata Sandi">
                        </div>
                    </div>
                    <div class="separator separator-content separator-dashed my-10 text-muted">Keamanan</div>
                    <div class="row mb-4">
                        <div class="col-4 form-group">
                            <label class="form-label">Port<sup class="ms-1 text-danger">*</sup></label>
                            <input type="number" name="port" class="form-control form-control-solid mt-2" value="{{ app_smtp_info()->port }}" placeholder="Port">
                        </div>
                        <div class="col-4 form-group">
                            <label class="form-label">Hostname<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="host" class="form-control form-control-solid mt-2" value="{{ app_smtp_info()->host }}" placeholder="Hostname">
                        </div>
                        <div class="col-4 form-group">
                            <label class="form-label">Sender<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="sender" class="form-control form-control-solid mt-2" value="{{ app_smtp_info()->sender }}" placeholder="Sender / Pengirim">
                        </div>
                    </div>
                    <div class="separator separator-content separator-dashed my-10 text-muted">Tambahan</div>
                    <div class="row mb-4">
                        <div class="col-6 form-group">
                            <label class="form-label">Protokol<sup class="ms-1 text-danger">*</sup></label>
                            <select class="form-select form-select-solid" name="protocol">
                                <option value="">Pilih salah satu</option>
                                @foreach (['ssl', 'tls'] as $protocol)
                                    <option value="{{ $protocol }}" {{ app_smtp_info()->protocol == $protocol ? 'selected' : '' }}>{{ strtoupper($protocol) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label">Status SMTP<sup class="ms-1 text-danger">*</sup></label>
                            <select class="form-select form-select-solid" name="is_active">
                                <option value="">Pilih salah satu</option>
                                @foreach ([1,2] as $status)
                                    @php $name = $status == 1 ? 'Aktif' : 'Tidak Aktif' @endphp
                                    <option value="{{ $status }}" {{ app_smtp_info()->is_active == $status ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('settings') }}" class="btn btn-light btn-light">Kembali</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</section>
@include('components.theme.pages.footer')