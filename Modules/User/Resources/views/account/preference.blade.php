@include('components.theme.pages.header')
<section>
    <div class="row">
        @php
            $url_segment = segment(3);
            if(empty($url_segment)) {
                $url_segment = '';
            }
        @endphp
        <x-theme.pages.account-preference :activeSetting="$url_segment" />
        <div class="col-md-12 mt-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Preferensi<span class="ms-2 fs-8 text-muted">Notifikasi</span></h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ site_url('user', 'account/update-preference') . '/' . user()->id }}">
                        @csrf
                        <div class="form-check form-switch form-check-custom form-check-solid mb-4">
                            <input class="form-check-input" type="checkbox" name="updateProduct" {{ !empty($getNotificationPreferences->onUpdateProduct) && $getNotificationPreferences->onUpdateProduct ? 'checked' : '' }} id="flexSwitchDefault"/>
                            <label class="form-check-label" for="flexSwitchDefault">
                                Terima pembaruan/informasi produk terbaru
                            </label>
                        </div>
                        <div class="form-check form-switch form-check-custom form-check-solid mb-4">
                            <input class="form-check-input" type="checkbox" name="updateNews" {{ !empty($getNotificationPreferences->onUpdateNews) && $getNotificationPreferences->onUpdateNews ? 'checked' : '' }} id="flexSwitchDefault"/>
                            <label class="form-check-label" for="flexSwitchDefault">
                                Terima informasi berita terbaru
                            </label>
                        </div>
                        <div class="form-check form-switch form-check-custom form-check-solid mb-4">
                            <input class="form-check-input" type="checkbox" name="updateOrder" {{ !empty($getNotificationPreferences->onUpdateOrders) && $getNotificationPreferences->onUpdateOrders ? 'checked' : '' }} id="flexSwitchDefault"/>
                            <label class="form-check-label" for="flexSwitchDefault">
                                Terima informasi detail pesanan secara realtime
                            </label>
                        </div>
                        <div class="form-group mt-8 mb-0">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ site_url('user', 'account') }}" class="btn btn-light btn-light">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
    Inputmask({
        mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
        greedy: false,
        onBeforePaste: function (pastedValue, opts) {
            pastedValue = pastedValue.toLowerCase();
            return pastedValue.replace("mailto:", "");
        },
        definitions: {
            "*": {
                validator: '[0-9A-Za-z!#$%&"*+/=?^_`{|}~\-]',
                cardinality: 1,
                casing: "lower"
            }
        }
    }).mask("#email");
</script>
@endpush
@include('components.theme.pages.footer')