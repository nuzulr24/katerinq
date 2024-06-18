@push('css')
    <link href="{{ frontend('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data-table" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Unik?</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="align-middle"></tbody>
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
            ajax: "{{ route('pages') }}",
            columns: [
                {data: null, name: 'id', className: 'text-center'},
                {data: 'title-post', name: 'title-post'},
                {data: 'unique', name: 'unique'},
                {data: 'status', name: 'status'},
                {data: null},
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
                                <a href="{{ app_url('pages/detail') }}/${row.id}" class="menu-link px-3">
                                    Detail
                                </a>
                            </div>
                            <!--end::Menu item-->

                            <div class="menu-item px-3">
                                <a href="{{ app_url('pages/edit') }}/${row.id}" class="menu-link px-3" data-kt-docs-table-filter="delete_row">
                                    Edit
                                </a>
                            </div>
                            
                            <div class="menu-item px-3">
                                <a href="#" data-url="{{ app_url('pages/delete') }}/${row.id}" class="menu-link px-3 deleteContent" data-kt-docs-table-filter="delete_row">
                                    Delete
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

        table.on('draw', function () { 
            KTMenu.createInstances();
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