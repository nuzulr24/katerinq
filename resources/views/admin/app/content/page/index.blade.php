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
                                    <th>Category</th>
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
            ajax: "{{ route('content') }}",
            columns: [
                {data: null, name: 'id', className: 'text-center'},
                {data: 'title-post', name: 'title-post'},
                {data: 'category', name: 'category'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            createdRow: function (row, data, dataIndex) {
                // Set the sequential number starting from 1
                $('td', row).eq(0).html(dataIndex + 1);
            }
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