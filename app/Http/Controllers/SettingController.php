<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Exception;

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
        
        $googleSheetsUrl = $settings['google_sheets_url'] ?? '';
        $googleSitesUrl = $settings['google_sites_url'] ?? '';
        $libraryName = $settings['library_name'] ?? 'Literawas';
        $loanDuration = $settings['loan_duration'] ?? 7;
        $lateFee = $settings['late_fee'] ?? 2000;
        $rewardPoints = $settings['reward_points'] ?? 10;

        return view('settings.index', compact(
            'googleSheetsUrl',
            'googleSitesUrl',
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
            'google_sheets_url' => 'nullable|url',
            'google_sites_url' => 'nullable|url',
        ], [
            'google_sheets_url.url' => 'Format URL Google Sheets tidak valid.',
            'google_sites_url.url' => 'Format URL Google Sites tidak valid.',
        ]);

        $settings = $request->only([
            'library_name',
            'loan_duration',
            'late_fee',
            'reward_points',
            'google_sheets_url',
            'google_sites_url'
        ]);

        $this->writeSettings($settings);

        return redirect()->route('settings.index')->with('success', 'Pengaturan perpustakaan berhasil diperbarui.');
    }

    /**
     * Synchronize data with Google Sheets.
     */
    public function syncSheets()
    {
        $settings = self::readSettings();
        $googleSheetsUrl = $settings['google_sheets_url'] ?? '';

        if (empty($googleSheetsUrl)) {
            return redirect()->route('settings.index')->with('error', 'URL Google Sheets Web App belum dikonfigurasi. Harap isi URL terlebih dahulu.');
        }

        try {
            // 1. Collect Books Data
            $books = Book::orderBy('title', 'asc')->get()->map(function ($book) {
                return [
                    'barcode' => $book->barcode,
                    'title' => $book->title,
                    'author' => $book->author,
                    'publisher' => $book->publisher,
                    'year' => $book->year,
                    'category' => $book->category,
                    'is_available' => (bool)$book->is_available,
                ];
            });

            // 2. Collect Members Data
            $members = Member::with('user')->get()->map(function ($member) {
                return [
                    'member_code' => $member->member_code,
                    'name' => $member->user->name ?? 'N/A',
                    'email' => $member->user->email ?? 'N/A',
                    'total_loans' => (int)$member->total_loans,
                    'points' => (int)$member->points,
                    'borrow_limit' => (int)$member->borrow_limit,
                    'joined_at' => $member->created_at ? $member->created_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            });

            // 3. Collect Borrows/Transactions Data
            $borrows = Borrow::with(['member.user', 'book'])->orderBy('borrow_date', 'desc')->get()->map(function ($borrow) {
                return [
                    'member_name' => $borrow->member->user->name ?? 'N/A',
                    'book_title' => $borrow->book->title ?? 'N/A',
                    'barcode' => $borrow->book->barcode ?? 'N/A',
                    'borrow_date' => $borrow->borrow_date ? $borrow->borrow_date->format('Y-m-d') : 'N/A',
                    'due_date' => $borrow->due_date ? $borrow->due_date->format('Y-m-d') : 'N/A',
                    'return_date' => $borrow->return_date ? $borrow->return_date->format('Y-m-d') : null,
                    'status' => $borrow->status,
                ];
            });

            // 4. Send POST request to Google Apps Script Web App
            $response = Http::timeout(25)->post($googleSheetsUrl, [
                'books' => $books,
                'members' => $members,
                'borrows' => $borrows,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['status']) && $result['status'] === 'success') {
                    return redirect()->route('settings.index')->with('success', 'Sinkronisasi dengan Google Sheets berhasil diselesaikan!');
                } else {
                    $msg = $result['message'] ?? 'Response status tidak sukses.';
                    return redirect()->route('settings.index')->with('error', 'Google Sheets merespon dengan kegagalan: ' . $msg);
                }
            } else {
                return redirect()->route('settings.index')->with('error', 'Koneksi ke Google Web App gagal. HTTP Status: ' . $response->status());
            }

        } catch (Exception $e) {
            return redirect()->route('settings.index')->with('error', 'Terjadi error saat sinkronisasi: ' . $e->getMessage());
        }
    }
}
