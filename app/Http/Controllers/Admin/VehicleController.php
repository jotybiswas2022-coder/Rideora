<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVehicleRequest;
use App\Http\Requests\Admin\UpdateVehicleRequest;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleImage;
use App\Support\FileUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'integer', 'exists:vehicle_categories,id'],
            'status' => ['nullable', 'in:'.implode(',', Vehicle::STATUSES)],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc,name_asc'],
        ]);

        $vehicles = Vehicle::query()
            ->with(['category', 'images', 'primaryImage'])
            ->withCount('bookings')
            ->when($filters['q'] ?? null, function ($query, $value) {
                $query->where(function ($inner) use ($value) {
                    $inner->where('name', 'like', '%'.$value.'%')
                        ->orWhere('brand', 'like', '%'.$value.'%')
                        ->orWhere('registration_number', 'like', '%'.$value.'%');
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $value) => $query->where('category_id', $value))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when(($filters['sort'] ?? 'newest') === 'price_asc', fn ($query) => $query->orderBy('price_per_day'))
            ->when(($filters['sort'] ?? 'newest') === 'price_desc', fn ($query) => $query->orderByDesc('price_per_day'))
            ->when(($filters['sort'] ?? 'newest') === 'name_asc', fn ($query) => $query->orderBy('name'))
            ->when(($filters['sort'] ?? 'newest') === 'newest', fn ($query) => $query->orderByDesc('created_at'))
            ->paginate(12)
            ->withQueryString();

        $categories = VehicleCategory::orderBy('name')->get();

        return view('admin.vehicles.index', compact('vehicles', 'categories', 'filters'));
    }

    public function create(): View
    {
        $categories = VehicleCategory::orderBy('name')->get();

        return view('admin.vehicles.create', compact('categories'));
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $vehicle = DB::transaction(function () use ($request) {
            $vehicle = Vehicle::create([
                'category_id' => $request->integer('category_id'),
                'name' => $request->string('name')->trim()->value(),
                'slug' => $this->uniqueSlug($request->string('name')->trim()->value()),
                'brand' => $request->string('brand')->trim()->value(),
                'model' => $request->filled('model') ? $request->string('model')->trim()->value() : null,
                'registration_number' => mb_strtoupper($request->string('registration_number')->trim()->value()),
                'vehicle_type' => $request->string('vehicle_type')->value(),
                'fuel_type' => $request->string('fuel_type')->value(),
                'transmission' => $request->string('transmission')->value(),
                'seats' => $request->integer('seats'),
                'price_per_hour' => $request->input('price_per_hour'),
                'price_per_day' => $request->input('price_per_day'),
                'security_deposit' => $request->input('security_deposit'),
                'location' => $request->filled('location') ? $request->string('location')->trim()->value() : null,
                'description' => $request->filled('description') ? $request->string('description')->trim()->value() : null,
                'status' => $request->string('status')->value(),
            ]);

            $this->storeImages($vehicle, $request);

            return $vehicle;
        });

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', 'Vehicle "'.$vehicle->name.'" added successfully.');
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['category', 'images']);
        $vehicle->loadCount(['bookings', 'reviews']);

        $recentBookings = $vehicle->bookings()
            ->with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.vehicles.show', compact('vehicle', 'recentBookings'));
    }

    public function edit(Vehicle $vehicle): View
    {
        $vehicle->load('images');
        $categories = VehicleCategory::orderBy('name')->get();

        return view('admin.vehicles.edit', compact('vehicle', 'categories'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        DB::transaction(function () use ($request, $vehicle) {
            $name = $request->string('name')->trim()->value();

            $vehicle->update([
                'category_id' => $request->integer('category_id'),
                'name' => $name,
                'slug' => $name !== $vehicle->name ? $this->uniqueSlug($name, $vehicle->id) : $vehicle->slug,
                'brand' => $request->string('brand')->trim()->value(),
                'model' => $request->filled('model') ? $request->string('model')->trim()->value() : null,
                'registration_number' => mb_strtoupper($request->string('registration_number')->trim()->value()),
                'vehicle_type' => $request->string('vehicle_type')->value(),
                'fuel_type' => $request->string('fuel_type')->value(),
                'transmission' => $request->string('transmission')->value(),
                'seats' => $request->integer('seats'),
                'price_per_hour' => $request->input('price_per_hour'),
                'price_per_day' => $request->input('price_per_day'),
                'security_deposit' => $request->input('security_deposit'),
                'location' => $request->filled('location') ? $request->string('location')->trim()->value() : null,
                'description' => $request->filled('description') ? $request->string('description')->trim()->value() : null,
                'status' => $request->string('status')->value(),
            ]);

            // Remove images the admin checked for deletion.
            $removeIds = array_filter((array) $request->input('remove_images', []));
            if ($removeIds) {
                $images = $vehicle->images()->whereIn('id', $removeIds)->get();
                foreach ($images as $image) {
                    FileUploader::delete($image->image);
                    $image->delete();
                }
            }

            $this->storeImages($vehicle, $request, 'new_primary_index');

            if ($request->filled('primary_image_id')) {
                $this->makePrimary($vehicle, $request->integer('primary_image_id'));
            }

            // Never leave a vehicle without a primary image.
            if (! $vehicle->images()->where('is_primary', true)->exists()) {
                $first = $vehicle->images()->orderBy('id')->first();
                if ($first) {
                    $first->update(['is_primary' => true]);
                }
            }
        });

        return redirect()
            ->route('admin.vehicles.edit', $vehicle)
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->bookings()->whereIn('booking_status', Booking::BLOCKING_STATUSES)->exists()) {
            return back()->with('error', 'This vehicle has active bookings. Cancel or close them first.');
        }

        DB::transaction(function () use ($vehicle) {
            foreach ($vehicle->images as $image) {
                FileUploader::delete($image->image);
            }

            $vehicle->images()->delete();
            $vehicle->delete();
        });

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }

    /**
     * Quick status change from the vehicle table.
     */
    public function toggleStatus(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Vehicle::STATUSES)],
        ]);

        $vehicle->update(['status' => $data['status']]);

        return back()->with('success', $vehicle->name.' is now marked as '.ucfirst($data['status']).'.');
    }

    public function makePrimaryImage(Vehicle $vehicle, VehicleImage $image): RedirectResponse
    {
        abort_unless($image->vehicle_id === $vehicle->id, 404);

        $this->makePrimary($vehicle, $image->id);

        return back()->with('success', 'Primary image updated.');
    }

    private function makePrimary(Vehicle $vehicle, int $imageId): void
    {
        $vehicle->images()->update(['is_primary' => false]);

        $vehicle->images()->whereKey($imageId)->update(['is_primary' => true]);
    }

    /**
     * Persist uploaded images and set the primary one.
     */
    private function storeImages(Vehicle $vehicle, Request $request, string $primaryIndexField = 'primary_index'): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $primaryIndex = $request->has($primaryIndexField) ? (int) $request->input($primaryIndexField) : 0;
        $hasPrimary = $vehicle->images()->where('is_primary', true)->exists();

        foreach ($request->file('images') as $index => $file) {
            $image = $vehicle->images()->create([
                'image' => FileUploader::store($file, 'vehicles'),
                'is_primary' => false,
            ]);

            if (! $hasPrimary && (int) $index === $primaryIndex) {
                $image->update(['is_primary' => true]);
                $hasPrimary = true;
            }
        }
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'vehicle';
        $slug = $base;
        $counter = 2;

        while (Vehicle::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
