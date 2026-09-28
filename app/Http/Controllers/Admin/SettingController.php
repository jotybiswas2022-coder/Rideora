<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        // Make sure every known setting exists before rendering the form.
        foreach (Setting::defaults() as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        Setting::flush();

        $settings = Setting::query()->pluck('value', 'key')->all();
        $defaults = Setting::defaults();

        return view('admin.settings.index', compact('settings', 'defaults'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $keys = array_keys(Setting::defaults());

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::put($key, $request->input($key));
            }
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Website settings saved successfully.');
    }
}
