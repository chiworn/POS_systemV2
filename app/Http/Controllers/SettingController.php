<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Display the general settings page.
     */
    public function index(): Response
    {
        return Inertia::render('backend/setting/GeneralSettings', [
            'settings' => Setting::getSettings(),
        ]);
    }

    /**
     * Update the general settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'store_name'    => 'required|string|max:255',
            'store_phone'   => 'nullable|string|max:50',
            'store_email'   => 'nullable|email|max:255',
            'store_address' => 'nullable|string|max:1000',
            'store_logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $settings = Setting::getSettings();

        $data = [
            'store_name'    => $request->store_name,
            'store_phone'   => $request->store_phone,
            'store_email'   => $request->store_email,
            'store_address' => $request->store_address,
        ];

        if ($request->hasFile('store_logo')) {
            // Remove old logo from S3 if exists
            if ($settings->store_logo) {
                $oldPath = str_replace(Storage::disk('s3')->url(''), '', $settings->store_logo);
                Storage::disk('s3')->delete($oldPath);
            }

            $path = $request->file('store_logo')->store('logos', 's3');
            $data['store_logo'] = Storage::disk('s3')->url($path);
        }

        $settings->update($data);

        return redirect()->back()->with('success', 'General settings updated successfully.');
    }

    /**
     * Display the about system page.
     */
    public function about(): Response
    {
        return Inertia::render('backend/setting/AboutSystem', [
            'systemVersion'  => 'v2.0.0',
            'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
            'phpVersion'     => PHP_VERSION,
            'vueVersion'     => '3.5.0',
            'developer'      => 'SYSTEM POS JOR Dev Team',
            'settings'       => Setting::getSettings(),
        ]);
    }

    /**
     * Display the tax settings page.
     */
    public function tax(): Response
    {
        $settings = Setting::getSettings();

        return Inertia::render('backend/setting/TaxSettings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update tax settings.
     */
    public function updateTax(Request $request)
    {
        $request->validate([
            'enable_tax' => 'required|boolean',
            'tax_rate'   => 'nullable|numeric|min:0|max:100',
            'tax_name'   => 'required|string|max:50',
            'tax_number' => 'nullable|string|max:100',
        ]);

        $settings = Setting::getSettings();
        $settings->update([
            'enable_tax' => $request->enable_tax,
            'tax_rate'   => $request->tax_rate ?? 0,
            'tax_name'   => $request->tax_name,
            'tax_number' => $request->tax_number,
        ]);

        return redirect()->back()->with('success', 'Tax settings updated successfully.');
    }
}
