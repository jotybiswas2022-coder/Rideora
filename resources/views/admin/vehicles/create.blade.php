@extends('admin.layouts.app')

@section('title', 'Add Vehicle')
@section('page-title', 'Add vehicle')
@section('page-subtitle', 'Create a new listing for the rental fleet')

@push('styles')
<style>
    .upload-zone {
        border: 1.5px dashed var(--border); border-radius: var(--radius); padding: 26px; text-align: center;
        background: var(--light); cursor: pointer;
    }
    .upload-zone:hover { border-color: var(--primary); }
    .preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin-top: 14px; }
    .preview-item { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: #fff; }
    .preview-item img { width: 100%; height: 92px; object-fit: cover; }
    .preview-item .label { padding: 7px 9px; font-size: .72rem; display: flex; align-items: center; justify-content: space-between; gap: 6px; }
</style>
@endpush

@section('content')
    <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data" class="stack-16">
        @csrf

        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Vehicle information</h3>
                    <p>Basic identification and classification</p>
                </div>
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light btn-sm">&larr; Back to list</a>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Vehicle name <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required placeholder="e.g. Toyota Axio">
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category <span style="color:var(--danger)">*</span></label>
                        <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="brand">Brand <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="brand" name="brand" class="form-control @error('brand') is-invalid @enderror"
                               value="{{ old('brand') }}" required placeholder="e.g. Toyota">
                        @error('brand')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="model">Model</label>
                        <input type="text" id="model" name="model" class="form-control @error('model') is-invalid @enderror"
                               value="{{ old('model') }}" placeholder="e.g. Axio Hybrid">
                        @error('model')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="registration_number">Registration number <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="registration_number" name="registration_number"
                               class="form-control @error('registration_number') is-invalid @enderror"
                               value="{{ old('registration_number') }}" required placeholder="e.g. DHA-GA-11-2345">
                        @error('registration_number')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="location">Pickup location</label>
                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror"
                               value="{{ old('location') }}" placeholder="e.g. Dhaka">
                        @error('location')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Specifications</h3>
                    <p>Technical details shown on the vehicle page</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="vehicle_type">Vehicle type <span style="color:var(--danger)">*</span></label>
                        <select id="vehicle_type" name="vehicle_type" class="form-control" required>
                            @foreach(\App\Models\Vehicle::VEHICLE_TYPES as $type)
                                <option value="{{ $type }}" @selected(old('vehicle_type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fuel_type">Fuel type <span style="color:var(--danger)">*</span></label>
                        <select id="fuel_type" name="fuel_type" class="form-control" required>
                            @foreach(\App\Models\Vehicle::FUEL_TYPES as $fuel)
                                <option value="{{ $fuel }}" @selected(old('fuel_type') === $fuel)>{{ $fuel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="transmission">Transmission <span style="color:var(--danger)">*</span></label>
                        <select id="transmission" name="transmission" class="form-control" required>
                            @foreach(\App\Models\Vehicle::TRANSMISSIONS as $transmission)
                                <option value="{{ $transmission }}" @selected(old('transmission') === $transmission)>{{ $transmission }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="seats">Seats <span style="color:var(--danger)">*</span></label>
                        <input type="number" id="seats" name="seats" min="1" max="60" class="form-control @error('seats') is-invalid @enderror"
                               value="{{ old('seats', 5) }}" required>
                        @error('seats')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span style="color:var(--danger)">*</span></label>
                        <select id="status" name="status" class="form-control" required>
                            @foreach(\App\Models\Vehicle::STATUSES as $status)
                                <option value="{{ $status }}" @selected(old('status', 'available') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <span class="form-hint">Only available and booked vehicles appear on the website.</span>
                    </div>

                    <div class="form-group">
                        <label for="security_deposit">Security deposit (৳) <span style="color:var(--danger)">*</span></label>
                        <input type="number" step="0.01" min="0" id="security_deposit" name="security_deposit"
                               class="form-control @error('security_deposit') is-invalid @enderror"
                               value="{{ old('security_deposit', 10000) }}" required>
                        @error('security_deposit')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="price_per_hour">Price per hour (৳) <span style="color:var(--danger)">*</span></label>
                        <input type="number" step="0.01" min="0" id="price_per_hour" name="price_per_hour"
                               class="form-control @error('price_per_hour') is-invalid @enderror"
                               value="{{ old('price_per_hour', 450) }}" required>
                        @error('price_per_hour')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="price_per_day">Price per day (৳) <span style="color:var(--danger)">*</span></label>
                        <input type="number" step="0.01" min="0" id="price_per_day" name="price_per_day"
                               class="form-control @error('price_per_day') is-invalid @enderror"
                               value="{{ old('price_per_day', 3500) }}" required>
                        @error('price_per_day')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group full">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Condition, included extras, pickup notes…">{{ old('description') }}</textarea>
                        @error('description')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Vehicle images</h3>
                    <p>Upload up to 8 images — JPG, PNG or WEBP, maximum 4 MB each</p>
                </div>
            </div>

            <div class="card-body">
                <label class="upload-zone" for="images">
                    <div style="font-size:1.7rem;"><i class="bi bi-camera"></i></div>
                    <strong style="display:block; margin:6px 0 2px;">Click to select images</strong>
                    <span class="muted small">The first image becomes the primary image unless you choose another below.</span>
                </label>

                <input type="file" id="images" name="images[]" class="form-control mt-8" accept="image/png,image/jpeg,image/webp"
                       multiple data-images-input>
                @error('images')<span class="form-error">{{ $message }}</span>@enderror
                @error('images.*')<span class="form-error">{{ $message }}</span>@enderror

                <div class="preview-grid" data-images-preview></div>

                <div class="form-group mt-16" data-primary-wrap style="display:none;">
                    <label for="primary_index">Primary image</label>
                    <select id="primary_index" name="primary_index" class="form-control" data-primary-select></select>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Save vehicle</button>
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light btn-lg">Cancel</a>
        </div>
    </form>
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
                item.innerHTML = '<img alt="preview"><div class="label"><span></span></div>';

                var reader = new FileReader();
                reader.onload = function (event) { item.querySelector('img').src = event.target.result; };
                reader.readAsDataURL(file);

                var name = file.name.length > 14 ? file.name.slice(0, 12) + '…' : file.name;
                item.querySelector('.label span').textContent = (index + 1) + '. ' + name;
                preview.appendChild(item);

                var option = document.createElement('option');
                option.value = index;
                option.textContent = (index + 1) + '. ' + name;
                if (index === 0) { option.selected = true; }
                primarySelect.appendChild(option);
            });

            primaryWrap.style.display = files.length > 1 ? 'flex' : 'none';
        });
    });
</script>
@endpush
