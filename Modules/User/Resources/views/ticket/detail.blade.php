@include('components.theme.pages.header')

<section>
    <!-- basic table -->
    <form method="POST" action="{{ route('user.ticket.update', segment(4)) }}">
    @csrf
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    @if(!empty($ticketResponse))
                        <div class="mb-4" style="max-height: 300px" data-simplebar>
                            @foreach($ticketResponse as $record)
                            @php $sender = $record->user->first()->name @endphp
                            @php $senderAvatar = substr($sender, 0, 1) @endphp
                            <div class="mb-5">           
                                <div class="card card-bordered w-100">   
                                    <div class="card-body">    
                                        <div class="w-100 d-flex flex-stack mb-8">
                                            <div class="d-flex align-items-center f">
                                                <div class="symbol symbol-50px me-5">
                                                    @if (\App\Models\User::find($record->user_id)->level == enum('isAdmin'))
                                                        @php
                                                            $level = "<span class='ms-1 text-muted fs-8'>admin</span>";
                                                        @endphp
                                                        <div class="symbol-label fs-1 fw-bold bg-primary text-white">
                                                            {{ $senderAvatar }}
                                                        </div>
                                                    @else
                                                        @php
                                                            $level = "<span class='ms-1 text-muted fs-8'>user</span>";
                                                        @endphp
                                                        <div class="symbol-label fs-1 fw-bold bg-dark text-white">
                                                            {{ $senderAvatar }}
                                                        </div>
                                                    @endif
                                                </div>          
                                                <div class="d-flex flex-column fw-semibold fs-5 text-gray-600 text-dark">    
                                                    <div class="d-flex align-items-center">    
                                                        <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-5 me-3">{{ $sender }} {!! $level !!}</a>
                                                        <span class="m-0"></span>               
                                                    </div>
                                                    <span class="text-muted fw-semibold fs-6">{{ $record->created_at }}</span>     
                                                </div>
                                            </div>
                                        </div>
                                
                                        <!--begin::Desc-->
                                        <p class="fw-normal fs-5 text-gray-700 m-0">
                                            {!! $record->message !!}
                                        </p> 
                                    </div>                    
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-danger d-flex align-items-center p-5">
                            <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-danger">Maaf! Pesan tidak ditemukan</h4>
                                <span>Tidak ditemukan pesan pada ticket, Silahkan kirim pesan</span>
                            </div>
                        </div>
                    @endif
                    <div> 
                        <textarea class="form-control form-control-solid placeholder-gray-600 fw-bold fs-4 ps-9 pt-7" rows="6" name="message" placeholder="Pesan" style="height: 149px;"></textarea>
                        <button type="submit" class="btn btn-primary mt-n20 position-relative float-end me-7">Kirim</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
</section>
@include('components.theme.pages.footer')