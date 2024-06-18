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
                    <h3 class="card-title">SEO</h3>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'settings.store.seo']) !!}
                    @csrf

                    <div class="row mb-4">
                        <div class="col-6 form-group">
                            <label class="form-label">Meta Title<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="meta_title" class="form-control form-control-solid mt-2" value="{{ app_info('meta_title') }}" placeholder="Meta Title">
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label">Meta Keyword<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="meta_keywords" class="form-control form-control-solid mt-2" value="{{ app_info('meta_keywords') }}" placeholder="Meta Keyword">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label mb-3">Meta Description<sup class="ms-1 text-danger">*</sup></label>
                        <textarea name="meta_description" id="meta_description" rows="4" class="form-control form-control-solid mt-2">{{ app_info('meta_description') }}</textarea>
                    </div>
                    <div class="separator separator-content separator-dashed my-10 text-muted">Script</div>
                    <div class="row mb-4">
                        <div class="col-6 form-group">
                            <label class="form-label">Google Tag Manager<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="gtag_manager" class="form-control form-control-solid mt-2" value="{{ app_info('gtag_manager') }}" placeholder="Google Tag Manager">
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label">Facebook Pixel<sup class="ms-1 text-danger">*</sup></label>
                            <input type="text" name="fb_pixel" class="form-control form-control-solid mt-2" value="{{ app_info('fb_pixel') }}" placeholder="Facebook Pixel">
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