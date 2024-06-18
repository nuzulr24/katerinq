@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    {{ Form::open(['route' => 'user.ticket.store']) }}
    @csrf
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Buat Tiket</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4">
                        <label class="form-label">Subjek<sup class="text-danger">*</sup></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" autocomplete="off" class="form-control form-control-solid mt-2 {{ $errors->has('subject') ? 'is-invalid' : '' }}" placeholder="Masukkan Subjek">
                        @error('subject') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label mb-4">Deskripsi<sup class="text-danger">*</sup></label>
                        <textarea name="message" id="message" class="form-control form-control-solid mt-2 {{ $errors->has('description') ? 'is-invalid' : '' }}">{{ old('message') }}</textarea>
                        @error('message') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label mb-4">Prioritas<sup class="text-danger">*</sup></label>
                        <select name="is_priority" class="form-select form-select-solid mt-2 {{ $errors->has('is_priority') ? 'is-invalid' : '' }}">
                            @foreach ([1,2,3] as $priority)
                                @php $name = $priority == 1 ? 'Normal' : ($priority == 2 ? 'Medium' : 'High')  @endphp
                                <option value="{{ $priority }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('is_priority') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">Buat</button>
                        <a href="{{ route('user.ticket') }}" class="btn btn-light btn-light">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</section>

@push('scripts')
    <script src="https://preview.keenthemes.com/html/metronic/docs/assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js"></script>
    <script>
    ClassicEditor
    .create(document.querySelector('#message'), {
        placeholder: 'Apa keluhan anda?',
        toolbar: {
            alignment: {
                options: ['left','right']
            },
            items: [
                'undo', 'redo',
                '|', 'heading',
                '|', 'bold', 'italic', 'underline', 'strikethrough',
                '|', 'link', 'insertTable',
                '|', 'bulletedList', 'numberedList', 'outdent', 'indent',
                '|', 'alignment','blockQuote',
                '|', 'horizontalLine',
            ]
        }
    });
    </script>
@endpush
@include('components.theme.pages.footer')