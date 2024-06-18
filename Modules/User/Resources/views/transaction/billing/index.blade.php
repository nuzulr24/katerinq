@push('css')
<link href="{{ frontend('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush
@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header flex-nowrap pt-5">
                    <div>
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-1 position-absolute ms-6"><span class="path1"></span><span class="path2"></span></i>
                            <input type="text" id="search" class="form-control form-control-solid w-250px ps-15" placeholder="Temukan Pesanan"/>
                        </div>
                    </div>
                    <div class="card-toolbar">
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
                                        Ditinjau
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" name="status" type="radio" value="2" id="flexRadioDefault"/>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        Dalam pengerjaan
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" name="status" type="radio" value="3" id="flexRadioDefault"/>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        Dikirim
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" name="status" type="radio" value="4" id="flexRadioDefault"/>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        Revisi
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" name="status" type="radio" value="5" id="flexRadioDefault"/>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        Selesai
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" name="status" type="radio" value="5" id="flexRadioDefault"/>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        Peninjauan Ditolak
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" name="status" type="radio" value="5" id="flexRadioDefault"/>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        Dibatalkan
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" name="status" type="radio" value="5" id="flexRadioDefault"/>
                                    <label class="form-check-label" for="flexRadioDefault">
                                        Ditolak
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
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data-table" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th>ID</th>
                                    <th>Subjek</th>
                                    <th>Biaya</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
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
        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('user.orders') }}",
            order: [[5, 'desc']],
            stateSave: true,
            columns: [
                {data: null, className: 'text-center', name: 'id'},
                {data: 'title-post', name: 'title-post'},
                {data: 'price', name: 'price'},
                {data: 'status', name: 'status'},
                {data: 'created_at', name: 'created_at'},
                {data: null, name: 'action', orderable: false, searchable: false},
            ],
            columnDefs: [
            {
                targets: -1,
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                            Aksi
                        </a>
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="{{ site_url('user', 'orders/v') . '/' }}${row.id}" class="menu-link px-3">
                                    Detail
                                </a>
                            </div>
                        </div>
                        <!--end::Menu-->
                    `;
                },
            }
            ],
            createdRow: function (row, data, dataIndex) {
                // Set the sequential number starting from 1
                $('td', row).eq(0).html(dataIndex + 1);
            }
        });

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
            table.search('orders|' + this.value).draw();
        });

        $('#data-table').on('click', '.deleteContent', function() {
            var url = $(this).data('url');
            $('#exampleModal').modal('show', {
                backdrop: 'static'
            });
            $('.link').attr('href', url)
        })
    });

</script>
@endpush
@include('components.theme.pages.footer')