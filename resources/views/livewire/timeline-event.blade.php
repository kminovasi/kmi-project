@php
use Carbon\Carbon;
@endphp

<div>
    <div class="py-4 mb-4">
        @foreach ($events as $event)
        @if ($loop->odd)
        <!-- timeline item ganjil -->
        <div class="row no-gutters">
            <div class="col-sm">
                <!--spacer-->
            </div>
            <!-- timeline item  center dot -->
            <div class="col-sm-1 text-center flex-column d-none d-sm-flex">
                <div class="row h-50">
                    <div class="col">&nbsp;</div>
                    <div class="col">&nbsp;</div>
                </div>
                <h5 class="m-2">
                    <span class="badge rounded-circle bg-danger">&nbsp;</span>
                </h5>
                <div class="row h-50">
                    <div class="col border-end">&nbsp;</div>
                    <div class="col">&nbsp;</div>
                </div>
            </div>
            <!-- timeline item 1 event content -->
            <div class="col-sm py-2">
                <div class="card border-danger shadow-sm">
                    <div class="card-body">
                        <div class="float-end text-danger small">{{ Carbon::parse($event->date_start)->format('d M') }}
                            - {{ Carbon::parse($event->date_end)->format('d M Y') }}</div>
                        <h4 class="card-title text-danger">{{ $event->event_name }} - {{ $event->event_year }}</h4>
                        <p class="card-text ">
                            <strong> Event lomba {{ $event->type }}</strong>
                        </p>
                        <p class="card-text">
                            Jadikan Kesempatan ini sebagai momen untuk menunjukkan kreativitas dan solusi terbaik Anda.
                            Berpartisipasilah dalam lomba ini dan jadilah bagian dari perubahan besar di lingkungan perusahaan.
                            Jangan lewatkan peluang untuk meraih peghargaan atas inovasi Anda!.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- timeline item genap -->
        <div class="row no-gutters">
            {{-- timeline item content --}}
            <div class="col-sm py-2">
                <div class="card border-danger shadow-sm">
                    <div class="card-body">
                        <div class="float-end text-danger small">{{ Carbon::parse($event->date_start)->format('d M') }}
                            - {{ Carbon::parse($event->date_end)->format('d M Y') }}</div>
                        <h4 class="card-title text-danger">{{ $event->event_name }} - {{ $event->event_year }}</h4>
                        <p class="card-text">
                           <strong> Event inovasi grup</strong></p>
                            <p class="card-text">
                                Jadikan Kesempatan ini sebagai momen untuk menunjukkan kreativitas dan solusi terbaik Anda.
                                Berpartisipasilah dalam lomba ini dan jadilah bagian dari perubahan besar di lingkungan perusahaan.
                                Jangan lewatkan peluang untuk meraih peghargaan atas inovasi Anda!.
                            </p>
                    </div>
                </div>
            </div>
            {{-- Timeline 2 Center dot --}}
            <div class="col-sm-1 text-center flex-column d-none d-sm-flex">
                <div class="row h-50">
                    <div class="col border-end">&nbsp;</div>
                    <div class="col">&nbsp;</div>
                </div>
                <h5 class="m-2">
                    <span class="badge rounded-pill bg-danger">&nbsp;</span>
                </h5>
                <div class="row h-50">
                    <div class="col border-end">&nbsp;</div>
                    <div class="col">&nbsp;</div>
                </div>
            </div>
            <div class="col-sm">
                <!--spacer-->
            </div>
        </div>
        @endif
        @endforeach

    </div>
</div>
