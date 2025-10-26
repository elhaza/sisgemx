<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $settings = Settings::first();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $settings = Settings::first() ?? Settings::create([]);
        $data = [];

        // Update school name
        if ($request->filled('school_name')) {
            $data['school_name'] = $request->input('school_name');
        }

        // Handle school logo upload
        if ($request->hasFile('school_logo')) {
            if ($settings->school_logo && Storage::disk('public')->exists($settings->school_logo)) {
                Storage::disk('public')->delete($settings->school_logo);
            }

            $logoPath = $request->file('school_logo')->store('logos', 'public');
            $data['school_logo'] = $logoPath;
        }

        // Handle institution logo upload (legacy)
        if ($request->hasFile('logo')) {
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        if (! empty($data)) {
            $settings->update($data);
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Configuración actualizada correctamente.');
    }
}
