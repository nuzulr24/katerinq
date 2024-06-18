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
        <div class="col-md-12 mb-5 mt-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Login</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                            <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                <tr>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-150px">User Agent</th>
                                    <th class="min-w-150px">Catatan</th>
                                    <th class="min-w-150px">IP Address</th>
                                    <th class="min-w-150px">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="fw-6 fw-semibold text-gray-600">
                                @foreach($logsActivity as $log)
                                    @php
                                        $logs = json_decode($log->withContent);
                                    @endphp
                                    <tr>
                                        <td><span class="badge badge-light-success fs-7 fw-bold">OK</span></td>
                                        <td>{{ $logs->user_agent }}</td>
                                        <td>{{ $logs->text }}</td>
                                        <td>{{ $logs->ip_address }}</td>
                                        <td>{{ date_formatting($log->performedOn, 'timeago') }}</td>
                                    </tr>
                                @endforeach 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Umum</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                            <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                <tr>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-150px">Tipe</th>
                                    <th class="min-w-150px">Catatan</th>
                                    <th class="min-w-150px">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="fw-6 fw-semibold text-gray-600">
                                @foreach($logGeneral as $log)
                                    @php
                                        $logs = json_decode($log->withContent);
                                    @endphp
                                    <tr>
                                        <td><span class="badge badge-light-success fs-7 fw-bold">OK</span></td>
                                        <td><span class="text-uppercase">{{ $logs->status }}</span></td>
                                        <td>{{ $logs->text }}</td>
                                        <td>{{ date_formatting($log->performedOn, 'timeago') }}</td>
                                    </tr>
                                @endforeach 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('components.theme.pages.footer')