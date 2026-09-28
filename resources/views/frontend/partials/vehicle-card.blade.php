@php
    /** @var \App\Models\Vehicle $vehicle */
    $rating = $vehicle->averageRating();
@endphp

<article class="v-card">
    <div class="v-card-media">
        <img src="{{ $vehicle->imageUrl() }}" alt="{{ $vehicle->name }}" loading="lazy">
        <span class="v-card-badge">{{ $vehicle->status === 'available' ? 'Available' : ucfirst($vehicle->status) }}</span>
        @if($vehicle->category)
            <span class="v-card-tag">{{ $vehicle->category->name }}</span>
        @endif
    </div>

    <div class="v-card-body">
        <div class="v-card-title">
            <div>
                <h3>{{ $vehicle->name }}</h3>
                <p class="muted small">{{ $vehicle->brand }}{{ $vehicle->model ? ' · '.$vehicle->model : '' }}</p>
            </div>
            @if($rating > 0)
                <span class="v-card-rating">{!! star_row($rating) !!}<span>{{ number_format($rating, 1) }}</span></span>
            @endif
        </div>

        <ul class="v-card-specs">
            <li><span><i class="bi bi-person"></i></span>{{ $vehicle->seats }} seats</li>
            <li><span><i class="bi bi-gear"></i></span>{{ $vehicle->transmission }}</li>
            <li><span><i class="bi bi-fuel-pump-fill"></i></span>{{ $vehicle->fuel_type }}</li>
        </ul>

        @if($vehicle->location)
            <p class="v-card-location"><span><i class="bi bi-geo-alt"></i></span>{{ $vehicle->location }}</p>
        @endif

        <div class="v-card-foot">
            <div class="v-card-price">
                <strong>{{ bdt($vehicle->price_per_day) }}</strong>
                <span class="muted small">/ day</span>
                <span class="muted small block">{{ bdt($vehicle->price_per_hour) }} / hour</span>
            </div>
            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-primary btn-sm">View Details</a>
        </div>
    </div>
</article>
