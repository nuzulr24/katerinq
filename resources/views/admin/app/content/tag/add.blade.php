@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Add</h5>
                </div>
                <div class="card-body">
                    {{ Form::open(['route' => 'content.tag.store']) }}
                    @csrf
                        <div class="form-group mb-3">
                            <label for="exampleEmail1">Name</label>
                            {{ Form::text('name', null, ['class' => 'form-control mt-2', 'id' => 'name', 'placeholder' => 'Enter Name']) }}
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('content.tag') }}" class="btn btn-light btn-light">Back</a>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</section>

@include('components.theme.pages.footer')