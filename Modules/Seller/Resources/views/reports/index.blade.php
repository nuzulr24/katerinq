@include('components.theme.pages.header')
<section>
    <div class="row">
        <div class="col-xl-3">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-purchase text-dark fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ $getTotalOrder }}
                    </div>
                    <div class="fw-semibold text-gray-400">
                        Total Pesanan
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-plus-square text-primary fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ 'Rp. ' . number_format($getTotalIncomeOrder, 0, ',', '.') }}
                    </div>
                    <div class="fw-semibold text-gray-400">
                        Total Pendapatan Semua
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-pin text-danger fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ 'Rp. ' . number_format($getTotalPendingIncomeOrder, 0, ',', '.') }}
                    </div>
                    <div class="fw-bold text-gray-400">
                        Total Pendapatan Pending
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <span class="pulse pulse-warning">
                        <i class="ki-outline ki-dots-square fs-2x ms-n1 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        <span class="pulse-ring" style="top: -15px; left: -10px"></span>
                    </span>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ $getListAwaitingWithdrawal }}</span>
                    </div>
                    <div class="fw-bold text-gray-400">
                        Penarikan Pending
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mb-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Flow Transaksi</h3>
                </div>
                <div class="card-body">
                    <div id="kt_apexcharts_3" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pesanan</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th>Invoice ID</th>
                                    <th>Jenis</th>
                                    <th>Pemesan</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @if(empty($getListOrder))
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada pesanan...</td>
                                    </tr>
                                @else
                                    @foreach($getListOrder as $order)
                                        <tr>
                                            <td>{{ $order->invoice_number }}</td>
                                            <td>
                                                @if($order->is_type == 1)
                                                    <span class="badge badge-light-success fs-7 fw-bold">Publisher</span>
                                                @else
                                                    <span class="badge badge-light-danger fs-7 fw-bold">Domain</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->user->first()->name }}</td>
                                            <td>
                                                @if ($row->is_status == enum('isOrderRequested'))
                                                    <span class="mb-1 badge font-medium bg-dark text-white py-3 px-4 fs-7 w-100 text-center">Menunggu</span>
                                                @elseif($row->is_status == enum('isOrderOnWorking'))
                                                    <span class="mb-1 badge font-medium bg-primary text-white py-3 px-4 fs-7 w-100 text-center">Dalam pengerjaan</span>
                                                @elseif($row->is_status == enum('isOrderSubmitted'))
                                                    <span class="mb-1 badge font-medium bg-danger text-white py-3 px-4 fs-7 w-100 text-center">Dikirim</span>
                                                @elseif($row->is_status == enum('isOrderCompleted'))
                                                    <span class="mb-1 badge font-medium bg-success text-white py-3 px-4 fs-7 w-100 text-center">Selesai</span>
                                                @elseif($row->is_status == enum('isOrderReqCancel'))
                                                    <span class="mb-1 badge font-medium bg-danger text-white py-3 px-4 fs-7 w-100 text-center">Permintaan Ditolak</span>
                                                @elseif($row->is_status == enum('isOrderCancelled'))
                                                    <span class="mb-1 badge font-medium bg-danger text-white py-3 px-4 fs-7 w-100 text-center">Dibatalkan</span>
                                                @elseif($row->is_status == enum('isOrderRejected'))
                                                    <span class="mb-1 badge font-medium bg-danger text-white py-3 px-4 fs-7 w-100 text-center">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('seller.orders.detail', ['id' => $order->id]) }}" class="btn btn-sm btn-light"><i class="ki-outline ki-arrow-up-right fs-4"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
    <script>
        var element = document.getElementById('kt_apexcharts_3');
        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = "#99a1b7";
        var borderColor = "#eceff1";
        var baseColor = "#7239ea";
        var lightColor = "#f8f5ff";

        @php
            $month = [];
            $profit = [];

            foreach($getFlowOrder as $keyMonth => $valueMonth) {
                array_push($month, ucfirst($keyMonth));
                array_push($profit, (int) $valueMonth);
            }
        @endphp

        var options = {
            series: [{
                name: 'Pendapatan',
                data: <?= json_encode($profit) ?>
            }],
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {

            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: 'solid',
                opacity: 1
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [baseColor]
            },
            xaxis: {
                categories: <?= json_encode($month) ?>,
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: baseColor,
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                style: {
                    fontSize: '12px'
                },
                y: {
                    formatter: function (val) {
                        return 'Rp. ' + val.toLocaleString('id-ID');
                    }
                }
            },
            colors: [lightColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            markers: {
                strokeColor: baseColor,
                strokeWidth: 3
            }
        };

        var chart = new ApexCharts(element, options);
        chart.render();
    </script>
@endpush
@include('components.theme.pages.footer')
