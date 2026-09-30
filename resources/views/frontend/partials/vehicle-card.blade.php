@php
    /** @var \App\Models\Vehicle $vehicle */
    // Use the eager-loaded average when the query provided one, otherwise fall back.
    $rating = array_key_exists('average_rating', $vehicle->getAttributes())
        ? round((float) $vehicle->getAttribute('average_rating'), 1)
        : $vehicle->averageRating();
@endphp

<article class="v-card">
    <div class="v-card-media">
        <img src="{{ $vehicle->imageUrl() }}" alt="" loading="lazy" decoding="async" width="640" height="400">
        <span class="v-card-badge {{ $vehicle->isAvailable() ? 'is-open' : 'is-busy' }}">{{ $vehicle->statusLabel() }}</span>
        @if($vehicle->category)
            <span class="v-card-tag">{{ $vehicle->category->name }}</span>
        @endif
        <span class="v-card-zoom" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
    </div>

    <div class="v-card-body">
        <div class="v-card-title">
            <h3><a href="{{ route('vehicles.show', $vehicle) }}">{{ $vehicle->name }}</a></h3>
            @if($rating > 0)
                <span class="v-card-rating">
                    {!! star_row($rating) !!}
                    <span class="num">{{ number_format($rating, 1) }}</span>
                </span>
            @endif
        </div>

        <p class="v-card-sub">
            {{ $vehicle->brand }}@if($vehicle->model) &middot; {{ $vehicle->model }}@endif
        </p>

        <ul class="v-card-specs">
            <li><span><i class="bi bi-people"></i></span>{{ $vehicle->seats }} seats</li>
            <li><span><i class="bi bi-gear"></i></span>{{ $vehicle->transmission }}</li>
            <li><span><i class="bi bi-fuel-pump-fill"></i></span>{{ $vehicle->fuel_type }}</li>
        </ul>

        @if($vehicle->location)
            <p class="v-card-location"><span><i class="bi bi-geo-alt-fill"></i></span>{{ $vehicle->location }}</p>
        @endif

        <div class="v-card-foot">
            <p class="v-card-price">
                <strong>{{ bdt($vehicle->price_per_day) }}</strong><span class="per">/ day</span>
                <span class="alt">{{ bdt($vehicle->price_per_hour) }} / hour</span>
            </p>
            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-primary btn-sm v-card-cta">
                Details<i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</article>
