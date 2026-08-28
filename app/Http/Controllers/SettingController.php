<?php

namespace App\Http\Controllers;

use App\Models\ReminderLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function reminder()
    {
        $daysBefore = Setting::get('reminder_days_before', '60,30,5');
        $daysAfter = Setting::get('reminder_days_after', '5');
        $recentReminderLogs = ReminderLog::with(['certification.user'])
            ->latest('sent_at')
            ->limit(10)
            ->get();

        return view('settings.reminder', compact('daysBefore', 'daysAfter', 'recentReminderLogs'));
    }

    public function updateReminder(Request $request)
    {
        $request->validate([
            'reminder_days_before' => ['required', 'string', 'regex:/^(\d+)(,\s*\d+)*$/'],
            'reminder_days_after' => ['required', 'string', 'regex:/^(\d+)(,\s*\d+)*$/'],
        ], [
            'reminder_days_before.regex' => 'Format harus berupa angka dipisah koma, misal: 60,30,5',
            'reminder_days_after.regex' => 'Format harus berupa angka dipisah koma, misal: 5,10',
        ]);

        Setting::set('reminder_days_before', $request->input('reminder_days_before'));
        Setting::set('reminder_days_after', $request->input('reminder_days_after'));

        return back()->with('success', 'Pengaturan jadwal reminder berhasil disimpan.');
    }
}
