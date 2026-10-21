<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginHistory;
use App\Models\User;

class LoginHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.view');
    }

    public function index(Request $request)
    {
        $query = LoginHistory::with('user');

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('identifier', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%")
                  ->orWhere('failure_reason', 'like', "%{$search}%");
            });
        }

        if ($from = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $histories = $query->latest('created_at')->paginate(25)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'user_code']);

        $metrics = [
            'total' => LoginHistory::count(),
            'success' => LoginHistory::where('status', 'Success')->count(),
            'failed' => LoginHistory::where('status', 'Failed')->count(),
            'locked' => LoginHistory::where('status', 'Locked')->count(),
        ];

        return view('login_history.index', compact('histories', 'users', 'metrics'));
    }
}
