<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin → always all users
        if ($user->role === 'admin') {
            $users = User::all();
        }

        // Tenant
        elseif ($user->role === 'tenant') {
            if ($user->tokenCan('users.read')) {
                $users = User::where('tenant_id', $user->tenant_id)->get();
            } else {
                $users = User::where('id', $user->id)->get();
            }
        }

        // Normal user
        else {
            $users = User::where('id', $user->id)->get();
        }

        // TODO: For API
        // return response()->json([
        //     'users' => $users
        // ]);
        
        // Pass the user data directly to the view using Inertia
        return Inertia::render('dashboard', [
            'users' => $users,
        ]);
    }
}
