@push('css')
<link href="{{ frontend('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush
@include('components.theme.pages.header')
<section>
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-5">
                <div class="card-body pt-9 pb-0">
                    <!--begin::Details-->
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                        <!--begin: Pic-->
                        <div class="me-7 mb-4">
                            <div class="symbol symbol-100px symbol-lg-160px symbol-fixed">
                                <img src="{{ gravatar_team($getSellerInfo['email']) }}" alt="image">
                            </div>
                        </div>
                        <!--end::Pic-->

                        <!--begin::Info-->
                        <div class="flex-grow-1">
                            <!--begin::Title-->
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <!--begin::User-->
                                <div class="d-flex flex-column">
                                    <!--begin::Name-->
                                    <div class="d-flex align-items-center mb-2">
                                        <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $getSellerInfo['name'] }}</a>
                                        {{-- <a href="#"><i class="ki-outline ki-verify fs-1 text-primary"></i></a> --}}
                                    </div>
                                    <!--end::Name-->

                                    <!--begin::Info-->                        
                                    <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                        <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                            <i class="ki-outline ki-profile-circle fs-4 me-1"></i> {{ $getSellerInfo->user->first()->name }}</a>
                                        <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                            <i class="ki-outline ki-profile-circle fs-4 me-1"></i> {{ $getSellerInfo['alias'] }}</a>
                                    </div>
                                    <!--end::Info-->                        
                                </div>
                            </div>
                            <!--end::Title-->

                            <!--begin::Stats-->
                            <div class="d-flex flex-wrap flex-stack">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-column flex-grow-1 pe-8">
                                    <!--begin::Stats-->
                                    <div class="d-flex flex-wrap">
                                        <!--begin::Stat-->
                                        <div class="border border-gray-300 border-dashed rounded min-w-200px py-3 px-4 me-6 mb-3">
                                            <!--begin::Number-->
                                            <div class="d-flex align-items-center">
                                                <i class="ki-outline ki-arrow-up fs-2 text-success me-2"></i>                                    <div class="fs-2 fw-bold counted" data-kt-countup="true" data-kt-countup-value="4500" data-kt-countup-prefix="$" data-kt-initialized="1">$4,500</div>
                                            </div>
                                            <!--end::Number-->

                                            <!--begin::Label-->
                                            <div class="fw-semibold fs-6 text-gray-400">Pendapatan</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Stat-->

                                        <!--begin::Stat-->
                                        <div class="border border-gray-300 border-dashed rounded min-w-200px py-3 px-4 me-6 mb-3">
                                            <!--begin::Number-->
                                            <div class="d-flex align-items-center">
                                                <i class="ki-outline ki-arrow-down fs-2 text-danger me-2"></i>                                    <div class="fs-2 fw-bold counted" data-kt-countup="true" data-kt-countup-value="75" data-kt-initialized="1">75</div>
                                            </div>
                                            <!--end::Number-->

                                            <!--begin::Label-->
                                            <div class="fw-semibold fs-6 text-gray-400">Tagihan</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Stat-->

                                        <!--begin::Stat-->
                                        <div class="border border-gray-300 border-dashed rounded min-w-200px py-3 px-4 me-6 mb-3">
                                            <!--begin::Number-->
                                            <div class="d-flex align-items-center">
                                                <i class="ki-outline ki-arrow-up fs-2 text-success me-2"></i>                                    <div class="fs-2 fw-bold counted" data-kt-countup="true" data-kt-countup-value="60" data-kt-countup-prefix="%" data-kt-initialized="1">%60</div>
                                            </div>
                                            <!--end::Number-->                                

                                            <!--begin::Label-->
                                            <div class="fw-semibold fs-6 text-gray-400">Rating</div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Stat-->
                                    </div>
                                    <!--end::Stats-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Details-->   
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Website</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-5">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-1 position-absolute ms-6"><span class="path1"></span><span class="path2"></span></i>
                            <input type="text" id="search" class="form-control form-control-solid w-250px ps-15" placeholder="Temukan Website"/>
                        </div>
                        <div class="ms-auto">
                            <!--begin::Menu-->
                            <button class="btn btn-light-primary btn-active-light-primary justify-content-end"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                data-kt-menu-overflow="true">
                                <i class="ki-outline ki-filter fs-1 me-n1"></i>
                                <span class="mb-0">Filter</span>
                            </button>

                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-300px"
                                data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">Filter By</div>
                                </div>
                                <div class="separator mb-3 opacity-75"></div>
                                <div class="px-7 py-5 menu-item" data-kt-docs-table-filter="status_type">
                                    <div class="form-check form-check-custom form-check-solid mb-3">
                                        <input class="form-check-input" name="status" type="radio" value="1" id="flexRadioDefault"/>
                                        <label class="form-check-label" for="flexRadioDefault">
                                            Aktif
                                        </label>
                                    </div>
                                    <div class="form-check form-check-custom form-check-solid mb-3">
                                        <input class="form-check-input" name="status" type="radio" value="2" id="flexRadioDefault"/>
                                        <label class="form-check-label" for="flexRadioDefault">
                                            Dalam Tinjau
                                        </label>
                                    </div>
                                    <div class="form-check form-check-custom form-check-solid mb-3">
                                        <input class="form-check-input" name="status" type="radio" value="3" id="flexRadioDefault"/>
                                        <label class="form-check-label" for="flexRadioDefault">
                                            Tidak Aktif
                                        </label>
                                    </div>
                                    <div class="form-check form-check-custom form-check-solid mb-3">
                                        <input class="form-check-input" name="status" type="radio" value="4" id="flexRadioDefault"/>
                                        <label class="form-check-label" for="flexRadioDefault">
                                            Ditolak
                                        </label>
                                    </div>
                                    <div class="form-check form-check-custom form-check-solid mb-3">
                                        <input class="form-check-input" name="status" type="radio" value="5" id="flexRadioDefault"/>
                                        <label class="form-check-label" for="flexRadioDefault">
                                            Ditutup
                                        </label>
                                    </div>

                                    <div class="d-flex justify-content-end py-3">
                                        <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-docs-table-filter="reset">Atur Ulang</button>
                                        <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-docs-table-filter="filter">Terapkan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="data-table" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th>ID</th>
                                    <th>URL</th>
                                    <th>Harga</th>
                                    <th>Bahasa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script src="{{ frontend('plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script type="text/javascript">
    $(function () {
        var filterStatus;

        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ app_url('users/sellers/detail') }}/{{ $id }}",
            order: [[1, 'desc']],
            stateSave: true,
            columns: [
                {data: null, name: 'id'},
                {data: 'title-post', name: 'title-post'},
                {data: 'post_price', name: 'post_price'},
                {data: 'language', name: 'language'},
                {data: 'is_status', name: 'is_status'},
            ],
            createdRow: function (row, data, dataIndex) {
                // Set the sequential number starting from 1
                $('td', row).eq(0).html(dataIndex + 1);
            }
        });

        // Filter Datatable
        var handleFilterDatatable = () => {
            // Select filter options
            filterStatus = document.querySelectorAll('[data-kt-docs-table-filter="status_type"] [name="status"]');
            const filterButton = document.querySelector('[data-kt-docs-table-filter="filter"]');

            // Filter datatable on submit
            filterButton.addEventListener('click', function () {
                let statusValue = 'status|';
                filterStatus.forEach(r => {
                    if (r.checked) {
                        statusValue = 'status|' + r.value;
                    }

                    if (statusValue === 'status|') {
                        statusValue = 'status|';
                    }
                });

                table.search(statusValue).draw();
            });
        }

        // Reset Filter
        var handleResetForm = () => {
            const resetButton = document.querySelector('[data-kt-docs-table-filter="reset"]');
            resetButton.addEventListener('click', function () {
                filterStatus[0].checked = true;
                table.search('').draw();
            });
        }

        table.on('draw', function () { 
            handleFilterDatatable();
            handleResetForm();
            KTMenu.createInstances();
        });

        table.search('').draw();

        // #myInput is a <input type="text"> element
        $('#search').on('keyup change', function () {
            table.search('sites|' + this.value).draw();
        });

        $('#data-table').on('click', '.deleteContent', function() {
            var url = $(this).data('url');
            $('#exampleModal').modal('show', {
                backdrop: 'static'
            });
            $('.link').attr('href', url)
        })

        $('#data-table').on('click', '.viewContent', function() {
            var url = $(this).data('id');
            $('#viewerModal').modal('show', {
                backdrop: 'static'
            });

            $.ajax({
                url: `{{ app_url('site/detail') }}/${url}`,
                type: 'GET',
                header: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if(data.secure) {
                        var iconSecure = `
                            <i class="ki-duotone ki-shield-tick fs-2 text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>                        
                        `
                    } else {
                        var iconSecure = `
                            <i class="ki-duotone ki-shield-cross fs-2 text-danger">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>                        
                        `
                    }
                    $('.result').html(`
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    ${iconSecure}
                                    <h6 class="ms-2 fs-2 mb-0 fw-bold">${data.url}</h6>
                                </div>
                            </div>
                            <div class="separator separator-dotted border-secondary my-6"></div>
                            <div class="col-4">
                                <div class="fw-bold">Pemilik URL</div>
                                <div class="text-muted">${data.is_seller}</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">Hak Kepemilikan</div>
                                <div class="text-muted">${data.is_role}</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">Jenis URL</div>
                                <div class="text-muted">${data.is_type}</div>
                            </div>
                            <div class="my-3"></div>
                            <div class="col-4">
                                <div class="fw-bold">Bahasa</div>
                                <div class="text-muted">${data.is_language}</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">Estimasi Pengerjaan</div>
                                <div class="text-muted">${data.is_delivery_time} Hari</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">Batas Kata</div>
                                <div class="text-muted">${data.is_word_limit} Kata</div>
                            </div>
                            <div class="my-3"></div>
                            <div class="col-4">
                                <div class="fw-bold">Harga Publish</div>
                                <div class="text-muted">${data.is_post_price}</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">Termasuk Konten</div>
                                <div class="text-muted">${data.is_content_included}</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">Harga Konten</div>
                                <div class="text-muted">${data.is_content_price}</div>
                            </div>
                            <div class="separator separator-dotted border-secondary my-3"></div>
                            <div class="col-6 text-center">
                                <div class="fw-bold">Domain Authority</div>
                                <div class="text-muted">${data.is_domain_authority}</div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="fw-bold">Page Authority</div>
                                <div class="text-muted">${data.is_page_authority}</div>
                            </div>
                            <div class="my-3"></div>
                            <div class="col-12 text-center">
                                <div class="fw-bold">Kategori URL</div>
                                <div class="text-muted">${data.is_url_category}</div>
                            </div>
                        </div>
                    `);
                }
            })
        })
    });

</script>
@endpush
@include('components.theme.pages.footer')