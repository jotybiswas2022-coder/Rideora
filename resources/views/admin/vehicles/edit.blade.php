@extends('admin.layouts.app')

@section('title', 'Edit '.$vehicle->name)
@section('page-title', 'Edit vehicle')
@section('page-subtitle', $vehicle->name.' · '.$vehicle->registration_number)

@push('styles')
<style>
    .upload-zone {
        border: 1.5px dashed var(--border); border-radius: var(--radius); padding: 22px; text-align: center;
        background: var(--light); cursor: pointer;
    }
    .upload-zone:hover { border-color: var(--primary); }
    .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 14px; }
    .image-card { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: #fff; }
    .image-card img { width: 100%; height: 128px; object-fit: cover; }
    .image-card .body { padding: 10px; display: grid; gap: 8px; }
    .image-card .tags { display: flex; gap: 6px; flex-wrap: wrap; }
    .preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin-top: 14px; }
    .preview-item { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: #fff; }
    .preview-item img { width: 100%; height: 92px; object-fit: cover; }
    .preview-item .label { padding: 7px 9px; font-size: .72rem; }
    .danger-zone { border-color: #FECACA; background: #FFF7F7; }
</style>
@endpush

@section('content')
    <div class="grid grid-sidebar">
        <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" enctype="multipart/form-data" class="stack-16">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Vehicle information</h3>
                        <p>Update listing details and specifications</p>
                    </div>
                    <div class="flex" style="gap:8px;">
                        <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn btn-light btn-sm">View</a>
                        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light btn-sm">Back</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Vehicle name <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $vehicle->name) }}" required>
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="category_id">Category <span style="color:var(--danger)">*</span></label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $vehicle->category_id) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="brand">Brand <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="brand" name="brand" class="form-control" value="{{ old('brand', $vehicle->brand) }}" required>
                            @error('brand')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" id="model" name="model" class="form-control" value="{{ old('model', $vehicle->model) }}">
                        </div>

                        <div class="form-group">
                            <label for="registration_number">Registration number <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="registration_number" name="registration_number"
                                   class="form-control @error('registration_number') is-invalid @enderror"
                                   value="{{ old('registration_number', $vehicle->registration_number) }}" required>
                            @error('registration_number')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="location">Pickup location</label>
                            <input type="text" id="location" name="location" class="form-control" value="{{ old('location', $vehicle->location) }}">
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="vehicle_type">Vehicle type <span style="color:var(--danger)">*</span></label>
                            <select id="vehicle_type" name="vehicle_type" class="form-control" required>
                                @foreach(\App\Models\Vehicle::VEHICLE_TYPES as $type)
                                    <option value="{{ $type }}" @selected(old('vehicle_type', $vehicle->vehicle_type) === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fuel_type">Fuel type <span style="color:var(--danger)">*</span></label>
                            <select id="fuel_type" name="fuel_type" class="form-control" required>
                                @foreach(\App\Models\Vehicle::FUEL_TYPES as $fuel)
                                    <option value="{{ $fuel }}" @selected(old('fuel_type', $vehicle->fuel_type) === $fuel)>{{ $fuel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="transmission">Transmission <span style="color:var(--danger)">*</span></label>
                            <select id="transmission" name="transmission" class="form-control" required>
                                @foreach(\App\Models\Vehicle::TRANSMISSIONS as $transmission)
                                    <option value="{{ $transmission }}" @selected(old('transmission', $vehicle->transmission) === $transmission)>{{ $transmission }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="seats">Seats <span style="color:var(--danger)">*</span></label>
                            <input type="number" id="seats" name="seats" min="1" max="60" class="form-control"
                                   value="{{ old('seats', $vehicle->seats) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span style="color:var(--danger)">*</span></label>
                            <select id="status" name="status" class="form-control" required>
                                @foreach(\App\Models\Vehicle::STATUSES as $status)
                                    <option value="{{ $status }}" @selected(old('status', $vehicle->status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <span class="form-hint">Set to maintenance to hide it from new bookings.</span>
                        </div>

                        <div class="form-group">
                            <label for="security_deposit">Security deposit (৳) <span style="color:var(--danger)">*</span></label>
                            <input type="number" step="0.01" min="0" id="security_deposit" name="security_deposit" class="form-control"
                                   value="{{ old('security_deposit', $vehicle->security_deposit) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="price_per_hour">Price per hour (৳) <span style="color:var(--danger)">*</span></label>
                            <input type="number" step="0.01" min="0" id="price_per_hour" name="price_per_hour" class="form-control"
                                   value="{{ old('price_per_hour', $vehicle->price_per_hour) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="price_per_day">Price per day (৳) <span style="color:var(--danger)">*</span></label>
                            <input type="number" step="0.01" min="0" id="price_per_day" name="price_per_day" class="form-control"
                                   value="{{ old('price_per_day', $vehicle->price_per_day) }}" required>
                        </div>

                        <div class="form-group full">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control">{{ old('description', $vehicle->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ Images ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Manage images</h3>
                        <p>Choose the primary image, delete outdated ones or upload new photos</p>
                    </div>
                </div>

                <div class="card-body">
                    @if($vehicle->images->isEmpty())
                        <div class="alert alert-warning">
                            <span><i class="bi bi-exclamation-triangle-fill"></i></span>
                            <div>This vehicle has no images yet. Upload at least one so it looks good on the website.</div>
                        </div>
                    @else
                        <div class="image-grid mb-24">
                            @foreach($vehicle->images as $image)
                                <div class="image-card">
                                    <img src="{{ $image->url() }}" alt="{{ $vehicle->name }} image">
                                    <div class="body">
                                        <div class="tags">
                                            @if($image->is_primary)
                                                {!! status_badge('Primary', 'badge-success') !!}
                                            @else
                                                {!! status_badge('Gallery', 'badge-muted') !!}
                                            @endif
                                        </div>

                                        @unless($image->is_primary)
                                            <button type="submit" name="primary_image_id" value="{{ $image->id }}"
                                                    class="btn btn-outline btn-sm" formnovalidate>Make primary</button>
                                        @endunless

                                        <label class="checkbox-row">
                                            <input type="checkbox" name="remove_images[]" value="{{ $image->id }}">
                                            <span>Delete this image</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <label class="upload-zone" for="images">
                        <div style="font-size:1.5rem;"><i class="bi bi-camera"></i></div>
                        <strong style="display:block; margin:6px 0 2px;">Add more images</strong>
                        <span class="muted small">JPG, PNG or WEBP · maximum 4 MB each · up to 8 per upload</span>
                    </label>

                    <input type="file" id="images" name="images[]" class="form-control mt-8" accept="image/png,image/jpeg,image/webp"
                           multiple data-images-input>
                    @error('images')<span class="form-error">{{ $message }}</span>@enderror
                    @error('images.*')<span class="form-error">{{ $message }}</span>@enderror

                    <div class="preview-grid" data-images-preview></div>

                    <div class="form-group mt-16" data-primary-wrap style="display:none;">
                        <label for="new_primary_index">Set a new image as primary</label>
                        <select id="new_primary_index" name="new_primary_index" class="form-control" data-primary-select></select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">Save changes</button>
                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn btn-light btn-lg">Cancel</a>
            </div>
        </form>

        <!-- ============ Sidebar ============ -->
        <aside>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Vehicle snapshot</h3>
                        <p>Current listing state</p>
                    </div>
                </div>
                <div class="card-body">
                    <img src="{{ $vehicle->imageUrl() }}" alt="{{ $vehicle->name }}"
                         style="width:100%; height:170px; object-fit:cover; border-radius: var(--radius); margin-bottom:16px;">

                    <div class="summary-list">
                        <div class="summary-row"><span>Status</span><strong>{!! status_badge($vehicle->statusLabel(), $vehicle->statusClass()) !!}</strong></div>
                        <div class="summary-row"><span>Category</span><strong>{{ $vehicle->category?->name ?? '—' }}</strong></div>
                        <div class="summary-row"><span>Bookings</span><strong>{{ $vehicle->bookings()->count() }}</strong></div>
                        <div class="summary-row"><span>Reviews</span><strong>{{ $vehicle->reviews()->count() }}</strong></div>
                        <div class="summary-row"><span>Rating</span><strong>{{ $vehicle->averageRating() ?: '—' }}</strong></div>
                        <div class="summary-row"><span>Daily rate</span><strong>{{ bdt($vehicle->price_per_day) }}</strong></div>
                        <div class="summary-row total"><span>Deposit</span><span>{{ bdt($vehicle->security_deposit) }}</span></div>
                    </div>

                    <div class="divider"></div>

                    <div class="stack-8">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline btn-block" target="_blank" rel="noopener">
                            View on website
                        </a>
                        <a href="{{ route('admin.bookings.index', ['q' => $vehicle->registration_number]) }}" class="btn btn-light btn-block">
                            Bookings for this vehicle
                        </a>
                    </div>
                </div>
            </div>

            <div class="card danger-zone">
                <div class="card-head">
                    <div>
                        <h3 style="color: var(--danger);">Danger zone</h3>
                        <p>Deleting a vehicle also removes its images</p>
                    </div>
                </div>
                <div class="card-body">
                    <p class="muted small mb-16">
                        Vehicles with active (pending, confirmed or ongoing) bookings cannot be deleted.
                    </p>
                    <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}"
                          data-confirm="Delete {{ $vehicle->name }} permanently?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">Delete vehicle</button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.querySelector('[data-images-input]');
        var preview = document.querySelector('[data-images-preview]');
        var primaryWrap = document.querySelector('[data-primary-wrap]');
        var primarySelect = document.querySelector('[data-primary-select]');
        if (!input || !preview) { return; }

        input.addEventListener('change', function () {
            preview.innerHTML = '';
            primarySelect.innerHTML = '';
            primaryWrap.style.display = 'none';

            var files = Array.from(input.files || []);
            if (!files.length) { return; }

            files.forEach(function (file, index) {
                if (!file.type.startsWith('image/')) { return; }

                var item = document.createElement('div');
                item.className = 'preview-item';
                item.innerHTML = '<img alt="preview"><div class="label"></div>';

                var reader = new FileReader();
                reader.onload = function (event) { item.querySelector('img').src = event.target.result; };
                reader.readAsDataURL(file);

                var name = file.name.length > 16 ? file.name.slice(0, 14) + '…' : file.name;
                item.querySelector('.label').textContent = (index + 1) + '. ' + name;
                preview.appendChild(item);

                var option = document.createElement('option');
                option.value = index;
                option.textContent = (index + 1) + '. ' + name;
                primarySelect.appendChild(option);
            });

            primaryWrap.style.display = files.length > 1 ? 'flex' : 'none';
        });
    });
</script>
@endpush
