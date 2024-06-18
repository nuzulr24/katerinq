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
                            <input type="text" id="search" class="form-control form-control-solid w-250px ps-15" placeholder="Temukan Nomer Tagihan"/>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data-table" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th>ID</th>
                                    <th>Kode Tagihan</th>
                                    <th>Pengguna</th>
                                    <th>Biaya</th>
                                    <th>Jenis Tagihan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold align-items-center"></tbody>
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
            ajax: "{{ route('billing') }}",
            order: [[1, 'desc']],
            stateSave: true,
            columns: [
                {data: null, className: 'text-center', name: 'id'},
                {data: 'ticket-invoice', name: 'ticket-invoice'},
                {data: 'author', name: 'author'},
                {data: 'price', name: 'price'},
                {data: 'type', name: 'type'},
                {data: 'status', name: 'status'},
            ],
            createdRow: function (row, data, dataIndex) {
                // Set the sequential number starting from 1
                $('td', row).eq(0).html(dataIndex + 1);
            }
        });

        table.search('').draw();

        // #myInput is a <input type="text"> element
        $('#search').on('keyup change', function () {
            table.search(this.value).draw();
        });
    });

</script>
@endpush
@include('components.theme.pages.footer')