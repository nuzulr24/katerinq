@push('css')
<link href="{{ frontend('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush
@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="accordion accordion-icon-toggle" id="kt_accordion_2">
                        <div class="">
                            <div class="accordion-header py-3 d-flex" data-bs-toggle="collapse" data-bs-target="#kt_accordion_2_item_1" aria-expanded="true">
                                <span class="accordion-icon">
                                    <i class="ki-duotone ki-arrow-right fs-4"><span class="path1"></span><span class="path2"></span></i>
                                </span>
                                <h3 class="fs-4 fw-semibold mb-0 ms-4">Add New Child Permission</h3>
                            </div>
                            <div id="kt_accordion_2_item_1" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_2">
                                {{ Form::open(['route' => 'permission.store.child']) }}
                                @csrf
                                    <div class="form-group mt-4 mb-4">
                                        <label class="form-label">Name of Child Permission<sup class="text-danger">*</sup></label>
                                        <input type="hidden" name="idParent" value="{{ $data['records']['id'] }}">
                                        <input type="text" name="name" value="{{ old('name') }}" autocomplete="off" class="form-control form-control-solid mt-2 {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Enter Name Initial">
                                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col mb-4">
                                            <label class="form-label">Guard Name<sup class="text-danger">*</sup></label>
                                            <select class="form-select form-select-solid mt-2 {{ $errors->has('guard_name') ? 'is-invalid' : '' }}" name="guard_name" data-control="select2" data-placeholder="Select an option">
                                                <option></option>
                                                @foreach($routeInApplication as $route)
                                                    <option value="{{ $route }}">{{ $route }}</option>
                                                @endforeach
                                            </select>
                                            @error('guard_name') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mb-0">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <a href="{{ route('permission') }}" class="btn btn-light btn-light">Back</a>
                                    </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    <!--end::Accordion-->
                </div>
            </div>
        </div>
        
        <div class="col-12 mt-5">
            <div class="card card-flush">
                <div class="card-header flex-nowrap pt-5">
                    <div>
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-outline ki-magnifier fs-1 position-absolute ms-6"><span class="path1"></span><span class="path2"></span></i>
                            <input type="text" id="search" class="form-control form-control-solid w-250px ps-15" placeholder="Search Rule"/>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data-table" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th>ID</th>
                                    <th>Name of Initial</th>
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
            ajax: "{{ route('permission.detail', $data['records']['slug']) }}",
            stateSave: true,
            columns: [
                {data: null, name: 'id'},
                {data: 'nameOfPermission', name: 'nameOfPermission'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            createdRow: function (row, data, dataIndex) {
                // Set the sequential number starting from 1
                $('td', row).eq(0).html(dataIndex + 1);
            }
        });

        // #myInput is a <input type="text"> element
        $('#search').on('keyup change', function () {
            table.search(this.value).draw();
        });

    });

</script>
@endpush
@include('components.theme.pages.footer')