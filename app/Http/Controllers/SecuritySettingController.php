<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SecuritySetting;
use App\Models\AuditLog;

class SecuritySettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:settings.view')->only(['index']);
        $this->middleware('permission:settings.edit')->only(['update']);
    }

    public function index()
    {
        $settings = SecuritySetting::all()->keyBy('key');
        return view('security.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'max_login_attempts' => 'required|integer|min:3|max:20',
            'lockout_minutes' => 'required|integer|min:1|max:1440',
            'password_min_length' => 'required|integer|min:6|max:64',
            'password_require_uppercase' => 'nullable|boolean',
            'password_require_numbers' => 'nullable|boolean',
            'password_require_symbols' => 'nullable|boolean',
            'session_lifetime_minutes' => 'required|integer|min:15|max:1440',
            'force_password_change_days' => 'required|integer|min:0|max:365',
        ]);

        $keys = [
            'max_login_attempts',
            'lockout_minutes',
            'password_min_length',
            'session_lifetime_minutes',
            'force_password_change_days',
        ];

        foreach ($keys as $key) {
            SecuritySetting::set($key, $request->input($key));
        }

        SecuritySetting::set('password_require_uppercase', $request->boolean('password_require_uppercase') ? '1' : '0');
        SecuritySetting::set('password_require_numbers', $request->boolean('password_require_numbers') ? '1' : '0');
        SecuritySetting::set('password_require_symbols', $request->boolean('password_require_symbols') ? '1' : '0');

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'security_settings_updated',
            'module' => 'settings',
            'table_name' => 'security_settings',
            'ip_address' => $request->ip(),
            'description' => 'Updated system security and password policies',
            'created_at' => now(),
        ]);

        return redirect()->route('security-settings.index')
            ->with('success', 'Security policies and authentication parameters have been successfully updated.');
    }
}
