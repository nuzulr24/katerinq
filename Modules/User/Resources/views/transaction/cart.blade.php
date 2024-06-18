@push('css')
<link href="{{ frontend('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush
@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    {!! Form::open(['route' => 'user.account.cart.store']) !!}
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Produk</h3>
                    @if(count($getListCart) > 0)
                        <div class="card-toolbar">
                            <button type="submit" class="btn btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary">Checkout</button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data-table" class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="d-flex">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="checkbox" name="selectAllCheckbox" value="1"/>
                                        </div>
                                    </th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach($getListCart as $items)
                                    <tr>
                                        <td class="w-10px pe-2">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" name="cartItem[]" value="{{ $items->id }}"/>
                                            </div>
                                        </td>
                                        <td>{{ $items->name }}</td>
                                        <td>{{ 'Rp. ' . number_format($items->price, 0, ',', '.') }}</td>
                                        <td>
                                            <a  href="#" data-url="{{ route('user.account.cart.delete', ['id' => $items->id]) }}" class="btn btn-sm btn-light btn-active-light-danger deleteContent">
                                                <i class="ki-outline ki-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>

@push('scripts')
<script src="{{ frontend('plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script type="text/javascript">
    $(function () {
        var table = $('#data-table').DataTable({
            searching: false,
            "columnDefs": [
                {
                    "orderable": false,
                    "targets": 0
                }
            ],
            "orderFixed": [0, 'asc'],
        });
        
        const initCart = () => {
            const allCheckboxes = document.querySelectorAll('tbody [type="checkbox"]');
            $('input[name=selectAllCheckbox]').attr('checked', true);
            allCheckboxes.forEach((checkbox) => {
                checkbox.checked = true;
            })
        }

        const toggleToolbar = () => {
            const allCheckboxes = document.querySelectorAll('tbody [type="checkbox"]');
            $('input[name=selectAllCheckbox]').on('click', function() {
                let checked = $(this).is(':checked');
                if(checked) {
                    allCheckboxes.forEach((checkbox) => {
                        checkbox.checked = true;
                    })
                } else {
                    allCheckboxes.forEach((checkbox) => {
                        checkbox.checked = false;
                    })
                }
            })
        }

        toggleToolbar();    
        initCart();
        
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