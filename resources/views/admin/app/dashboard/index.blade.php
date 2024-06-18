@include('components.theme.pages.header')
<section>
    <div class="row">
        <div class="col-md-3 mb-2">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-purchase text-dark fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ $getTotalOrders }}
                    </div>
                    <div class="fw-semibold text-gray-400">
                        Total Pesanan
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-plus-square text-primary fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ 'Rp. ' . number_format($getIncomeOrder, 0, ',', '.') }}
                    </div>
                    <div class="fw-semibold text-gray-400">
                        Total Pendapatan
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                <div class="card-body">
                    <i class="ki-outline ki-pin text-danger fs-2x ms-n1"></i>
                    <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                        {{ $getTotalWebsite }}
                    </div>
                    <div class="fw-bold text-gray-400">
                        Total Produk
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
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
        <div class="col-md-8 mt-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Flow Transaksi</h3>
                </div>
                <div class="card-body">
                    <div id="kt_apexcharts_3" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aktifitas terakhir</h3>
                </div>
                <div style="height: 368px;" data-simplebar>
                    <ul class="list-group list-group-flush">
                        @if (empty($getListActivity))
                            Tidak ditemukan aktifitas
                        @else
                            @foreach($getListActivity as $value)
                                @php
                                    $values = json_decode($value->withContent);
                                @endphp
                                <li class="py-2 d-flex list-group-item align-items-center border border-1 border-bottom-dashed" style="border-top: none !important; border-left: none !important; border-right: none  !important;">
                                    <div class="symbol symbol-25px symbol-circle">
                                        <div class="symbol-label" style="background-image:url({{ gravatar_team($value->user->first()->email) }})"></div>
                                    </div>
                                    <span class="user-name ms-4">
                                        <p class="mb-0"><span class="fw-bold">{{ $value->user->first()->name }}</span></p>
                                        <p class="small text-gray-400 mb-0">{{ $values->text }}</p>
                                    </span>
                                </li>
                            @endforeach
                        @endif
                    </ul>
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
