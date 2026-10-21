<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\AuditLog;
use App\Models\SecuritySetting;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();
        $user->load(['role.permissions', 'loginHistories' => function ($q) {
            $q->latest('created_at')->take(10);
        }, 'activities' => function ($q) {
            $q->latest('created_at')->take(10);
        }]);

        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $pictureName = time() . '_' . Str::slug($user->username ?? 'user') . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/avatars');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $pictureName);
            $user->profile_picture = $pictureName;
        }

        $user->name = $request->name;
        $user->email = strtolower(trim($request->email));
        $user->mobile = $request->mobile;
        $user->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'profile_updated',
            'module' => 'profile',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => 'User updated their personal profile details',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Your profile details have been successfully updated.');
    }

    public function changePassword(Request $request)
    {
        $minLength = (int) SecuritySetting::get('password_min_length', 8);

        $rules = [
            'current_password' => 'required',
            'password' => ['required', 'string', 'confirmed', 'min:' . $minLength],
        ];

        if (SecuritySetting::get('password_require_uppercase') == '1') {
            $rules['password'][] = 'regex:/[A-Z]/';
        }
        if (SecuritySetting::get('password_require_numbers') == '1') {
            $rules['password'][] = 'regex:/[0-9]/';
        }
        if (SecuritySetting::get('password_require_symbols') == '1') {
            $rules['password'][] = 'regex:/[@$!%*#?&]/';
        }

        $request->validate($rules, [
            'password.regex' => 'The password must satisfy the required character complexity criteria.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'The current password you entered is incorrect.']);
        }

        if (Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors(['password' => 'Your new password cannot be the same as your current password.']);
        }

        $user->password_hash = Hash::make($request->password);
        $user->must_change_password = false;
        $user->password_changed_at = now();
        $user->remember_token = Str::random(60); // Invalidate other sessions
        $user->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'password_changed',
            'module' => 'profile',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => 'User successfully changed their account password',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Your password has been changed successfully.');
    }

    public function revokeOtherSessions(Request $request)
    {
        $user = Auth::user();
        $user->remember_token = Str::random(60);
        $user->save();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'user_revoked_other_sessions',
            'module' => 'profile',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => 'User revoked all other active sessions and tokens',
            'created_at' => now(),
        ]);

        return back()->with('success', 'All other active sessions and tokens have been invalidated.');
    }
}
