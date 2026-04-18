<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'company_name' => Setting::getValue('company_name', 'CV MIRSA CIPTA INDONESIA'),
            'company_slogan' => Setting::getValue('company_slogan', 'Designing and manufacture for Jig, SPM and Mechanical component'),
            'app_version' => Setting::getValue('app_version', '1.0.0'),
            'default_unit' => Setting::getValue('default_unit', 'Pcs'),
            'currency_symbol' => Setting::getValue('currency_symbol', 'IDR'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_slogan' => 'required|string|max:255',
            'app_version' => 'nullable|string|max:50',
            'default_unit' => 'required|string|max:20',
            'currency_symbol' => 'required|string|max:10',
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('settings.index')->with('status', 'Pengaturan berhasil diperbarui!');
    }
}
