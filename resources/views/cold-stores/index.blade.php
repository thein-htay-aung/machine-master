@extends('layouts.app')

@section('content')
    @php
        $totalStores = count($stores);
        $reportingStores = $latestTemperatures->count();
        $freshStores = $latestTemperatures->filter(fn ($temperature) => $temperature->recorded_at->diffInMinutes(now()) <= 10)->count();
    @endphp

    <style>
        .cold-store-page { max-width: 1440px; }
        .cold-store-hero { overflow: hidden; position: relative; background: linear-gradient(120deg, #082f49 0%, #0c4a6e 52%, #0369a1 100%); border-radius: 1.25rem; color: #fff; }
        .cold-store-hero::after { content: ''; position: absolute; width: 18rem; height: 18rem; right: -6rem; top: -11rem; border: 2.5rem solid rgba(186, 230, 253, .12); border-radius: 50%; }
        .cold-store-hero .hero-content { position: relative; z-index: 1; }
        .cold-store-kicker { color: #bae6fd; font-size: .72rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
        .cold-store-stat { min-width: 112px; background: rgba(255, 255, 255, .11); border: 1px solid rgba(255, 255, 255, .16); border-radius: .85rem; backdrop-filter: blur(8px); }
        .cold-store-card { border: 1px solid #e7eef5; border-radius: 1rem; box-shadow: 0 .35rem 1.2rem rgba(15, 23, 42, .055); transition: transform .2s ease, box-shadow .2s ease; }
        .cold-store-card:hover { transform: translateY(-3px); box-shadow: 0 .85rem 1.75rem rgba(15, 23, 42, .11); }
        .cold-store-icon { width: 2.8rem; height: 2.8rem; display: inline-flex; align-items: center; justify-content: center; border-radius: .8rem; background: #e0f2fe; color: #0284c7; font-size: 1.35rem; }
        .cold-store-value { color: #0f172a; font-size: clamp(2.25rem, 5vw, 3.1rem); font-weight: 700; letter-spacing: -.065em; line-height: 1; }
        .cold-store-unit { color: #64748b; font-size: 1rem; font-weight: 600; letter-spacing: 0; }
        .cold-store-updated { color: #64748b; font-size: .78rem; }
        @media (max-width: 575.98px) { .cold-store-hero { border-radius: 1rem; } .cold-store-stat { min-width: 0; flex: 1; } }
    </style>

    <div class="container-fluid cold-store-page px-2 px-sm-3 py-2">
        <section class="cold-store-hero p-3 p-md-4 mb-4">
            <div class="hero-content d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
                <div>
                    <div class="cold-store-kicker mb-2"><i class="bi bi-snow2 me-1" aria-hidden="true"></i>Temperature monitoring</div>
                    <h1 class="h3 fw-semibold mb-1">Cold Stores</h1>
                    <p class="mb-0 text-white-50">Latest sensor temperatures, refreshed every five minutes.</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="cold-store-stat px-3 py-2">
                        <div class="small text-white-50">Reporting</div>
                        <div class="fs-5 fw-semibold">{{ $reportingStores }}<span class="small fw-normal text-white-50">/{{ $totalStores }}</span></div>
                    </div>
                    <div class="cold-store-stat px-3 py-2">
                        <div class="small text-white-50">Up to date</div>
                        <div class="fs-5 fw-semibold">{{ $freshStores }}<span class="small fw-normal text-white-50">/{{ $totalStores }}</span></div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-3">
            @foreach($stores as $key => $name)
                @php
                    $reading = $latestTemperatures->get($key);
                @endphp
                <div class="col-12 col-sm-6 col-xl-4">
                    <article class="card cold-store-card h-100 border-0">
                        <div class="card-body p-3 p-md-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div class="min-w-0">
                                    <h2 class="h5 mb-1 text-truncate">{{ $name }}</h2>
                                </div>
                                <span class="cold-store-icon flex-shrink-0"><i class="bi bi-thermometer-snow" aria-hidden="true"></i></span>
                            </div>

                            @if($reading)
                                <div class="mt-4 mb-4">
                                    <span class="cold-store-value">{{ number_format($reading->temperature, 1) }}</span><span class="cold-store-unit ms-1">&deg;C</span>
                                </div>
                                <div class="cold-store-updated mt-auto"><i class="bi bi-clock-history me-1" aria-hidden="true"></i>{{ $reading->recorded_at->format('d M Y, h:i A') }}</div>
                            @else
                                <div class="mt-4 mb-4 text-secondary">
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
@endsection
