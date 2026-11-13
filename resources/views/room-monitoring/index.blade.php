@extends('layouts.app')

@section('content')
    @php
        $freshCount = $statuses->filter(fn ($status) => \Carbon\Carbon::parse($status->recorded_at)->diffInMinutes(now()) <= 3)->count();
    @endphp

    <style>
        .room-monitoring { max-width: 1440px; }
        .room-hero { background: linear-gradient(120deg, #164e63, #0e7490); border-radius: 1.25rem; color: #fff; }
        .room-card { border: 1px solid #dbeafe; border-radius: 1rem; box-shadow: 0 .35rem 1.2rem rgba(15, 23, 42, .06); }
        .room-icon { width: 2.8rem; height: 2.8rem; display: inline-flex; align-items: center; justify-content: center; border-radius: .8rem; background: #e0f2fe; color: #0891b2; font-size: 1.35rem; }
        .room-value { font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 700; letter-spacing: -.06em; line-height: 1; color: #0f172a; }
    </style>

    <div class="container-fluid room-monitoring px-2 px-sm-3 py-2">
        <section class="room-hero p-3 p-md-4 mb-4 d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="small text-white-50 text-uppercase fw-semibold mb-2">Live environmental monitoring</div>
                <h1 class="h3 fw-semibold mb-1">Room Temp &amp; Humidity</h1>
                <p class="mb-0 text-white-50">Updates automatically every minute. Historical readings are retained every 15 minutes.</p>
            </div>
            <div class="text-end">
                <div class="small text-white-50">Online</div>
                <div class="fs-4 fw-semibold">{{ $freshCount }}/{{ count($locations) }}</div>
            </div>
        </section>

        <div class="row g-3">
            @foreach($locations as $key => $name)
                @php 
                    $status = $statuses->get($key); 
                    $fresh = $status && \Carbon\Carbon::parse($status->recorded_at)->diffInMinutes(now()) <= 3; 
                @endphp
                <div class="col-12 col-sm-6 col-xl-3">
                    <article class="card room-card h-100 border-0">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex justify-content-between gap-2 align-items-start">
                                <h2 class="h5 mb-0">{{ $name }}</h2>
                                <span class="room-icon">
                                    <i class="bi bi-thermometer-half"></i>
                                </span>
                            </div>
                            @if($status)
                                <div class="mt-4">
                                    <span class="room-value">{{ number_format($status->temperature, 1) }}</span>
                                    <span class="text-secondary ms-1">&deg;C</span>
                                </div>
                                <div class="mt-2">
                                    <span class="fs-5 fw-semibold">{{ number_format($status->humidity, 1) }}%</span>
                                    <span class="text-secondary ms-1">Humidity</span>
                                </div>
                                <div class="small mt-4 {{ $fresh ? 'text-success' : 'text-danger' }}">
                                    <i class="bi bi-circle-fill me-1"></i>
                                    {{ $fresh ? 'Online' : 'No recent update' }}
                                </div>
                                <div class="small text-secondary mt-1">
                                    Updated {{ \Carbon\Carbon::parse($status->recorded_at)->format('d M Y, h:i A') }}
                                </div>
                            @else
                                <div class="mt-4 text-secondary">
                                    <div class="fs-5 fw-semibold">No reading yet</div>
                                    <div class="small mt-1">Waiting for this sensor to report.</div>
                                </div>
                            @endif
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        setTimeout(function () { window.location.reload(); }, 60000);
    </script>
@endsection
