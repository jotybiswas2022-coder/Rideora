<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Models\VehicleCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = VehicleCategory::query()
            ->withCount('vehicles')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = VehicleCategory::create([
            'name' => $request->string('name')->trim()->value(),
            'slug' => $this->uniqueSlug($request->string('name')->trim()->value()),
            'icon' => $request->filled('icon') ? $request->string('icon')->trim()->value() : null,
            'description' => $request->filled('description') ? $request->string('description')->trim()->value() : null,
            'status' => $request->string('status')->value(),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category "'.$category->name.'" created successfully.');
    }

    public function edit(VehicleCategory $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(StoreCategoryRequest $request, VehicleCategory $category): RedirectResponse
    {
        $name = $request->string('name')->trim()->value();

        $category->update([
            'name' => $name,
            'slug' => $name !== $category->name ? $this->uniqueSlug($name, $category->id) : $category->slug,
            'icon' => $request->filled('icon') ? $request->string('icon')->trim()->value() : null,
            'description' => $request->filled('description') ? $request->string('description')->trim()->value() : null,
            'status' => $request->string('status')->value(),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(VehicleCategory $category): RedirectResponse
    {
        if ($category->vehicles()->exists()) {
            return back()->with('error', 'This category still has vehicles assigned. Move them first.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $counter = 2;

        while (VehicleCategory::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
