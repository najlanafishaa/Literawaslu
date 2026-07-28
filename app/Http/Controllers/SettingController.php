<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Get settings storage path.
     */
    private static function getSettingsPath()
    {
        return 'settings.json';
    }

    /**
     * Read settings from storage.
     */
    public static function readSettings()
    {
        $path = self::getSettingsPath();
        if (Storage::exists($path)) {
            return json_decode(Storage::get($path), true) ?: [];
        }
        return [];
    }

    /**
     * Write settings to storage.
     */
    private function writeSettings(array $settings)
    {
        Storage::put(self::getSettingsPath(), json_encode($settings, JSON_PRETTY_PRINT));
    }

    /**
     * Get specific setting helper.
     */
    public static function getSetting($key, $default = null)
    {
        $settings = self::readSettings();
        return $settings[$key] ?? $default;
    }

    /**
     * Display settings page.
     */
    public function index()
    {
        $settings = self::readSettings();
        
        $libraryName = $settings['library_name'] ?? 'Literawaslu';
        $loanDuration = $settings['loan_duration'] ?? 7;
        $lateFee = $settings['late_fee'] ?? 2000;
        $rewardPoints = $settings['reward_points'] ?? 10;

        return view('settings.index', compact(
            'libraryName',
            'loanDuration',
            'lateFee',
            'rewardPoints'
        ));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'library_name' => 'required|string|max:255',
            'loan_duration' => 'required|integer|min:1',
            'late_fee' => 'required|integer|min:0',
            'reward_points' => 'required|integer|min:0',
        ]);

        $settings = $request->only([
            'library_name',
            'loan_duration',
            'late_fee',
            'reward_points'
        ]);

        $this->writeSettings($settings);

        return redirect()->route('settings.index')->with('success', 'Pengaturan perpustakaan berhasil diperbarui.');
    }
}
