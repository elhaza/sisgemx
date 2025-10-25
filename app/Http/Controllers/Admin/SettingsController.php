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

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->update(['logo_path' => $logoPath]);
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Configuración actualizada correctamente.');
    }
}
