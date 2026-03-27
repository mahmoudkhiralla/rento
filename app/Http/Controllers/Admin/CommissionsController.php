<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class CommissionsController extends Controller
{
    public function index()
    {
        // Get commission settings
        $settings = $this->getSettings();

        return view('dashboard.payments.commissions', compact('settings'));
    }

    private function getSettings()
    {
        return [
            'commission_percentage' => Setting::get('commission_percentage', 25),
            'commission_calculation_method' => Setting::get('commission_calculation_method', 'percentage'),
            'commission_fixed_value' => Setting::get('commission_fixed_value', 0),
            'points_enabled' => Setting::get('points_enabled', false),
            'points_per_transaction' => Setting::get('points_per_transaction', 100),
            'points_per_dinar' => Setting::get('points_per_dinar', 100),
            'min_points_conversion' => Setting::get('min_points_conversion', 5),
        ];
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_fixed_value' => 'nullable|numeric|min:0',
            'commission_calculation_method' => 'required|in:percentage,fixed',
            'points_enabled' => 'boolean',
            'points_per_transaction' => 'required|integer|min:0',
            'points_per_dinar' => 'nullable|integer|min:1',
            'min_points_conversion' => 'required|numeric|min:0',
        ]);

        if ($validated['commission_calculation_method'] === 'percentage') {
            $validated['commission_percentage'] = $validated['commission_percentage'] ?? 0;
            $validated['commission_fixed_value'] = $validated['commission_fixed_value'] ?? 0;
        } else {
            $validated['commission_fixed_value'] = $validated['commission_fixed_value'] ?? 0;
            $validated['commission_percentage'] = $validated['commission_percentage'] ?? 0;
        }

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإعدادات بنجاح',
        ]);
    }
}
