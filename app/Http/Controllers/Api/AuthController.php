<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stancl\Tenancy\Database\Models\Tenant;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tenantId = 0;

        $user = User::create([
            'name' => $request->name,
            'tenant_id' => $tenantId,
            'email' => $request->email,
            'role' => 'user',
            'password' => Hash::make($request->password),
        ]);

        $scopes = ['users.read', 'products.read'];

        $token = $user->createToken('API Token', $scopes)->accessToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        // $token = $user->createToken('API Token', ['users.read', 'products.read'])->accessToken;

        $scopes = [];

        if ($user->role == 'tenant') {
            $scopes = ['users.read', 'products.read', 'orders.read', 'admin'];
        } else {
            $scopes = ['users.read', 'products.read', 'orders.read', 'view-products'];
        }

        $token = $user->createToken('API Token', $scopes)->accessToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function userpermission(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'User not authenticated'], 401);

        $permissions = [
            'users.read',
            'users.write',
            'products.read',
            'products.write',
            'view-products',
            'add-products',
            'admin',
            'orders.read'
        ];

        $userPermissions = array_filter($permissions, fn($permission) => $user->token()->can($permission));

        $tenant = Tenant::find($user->tenant_id) ?: Tenant::where('name', $user->name)->first();

        $oauthToken = DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->where('revoked', 0) 
            ->first();

        // Fetch the scopes from the OAuth token if available
        $tokenScopes = $oauthToken ? json_decode($oauthToken->scopes) : [];
        // COMMENTS: Only Token Scopes Fetch
        // return response()->json(['user_permissions' => array_values($userPermissions)]);
        return response()->json(['dbscopes' => $tenant->scopes, 'tokenscopes' => array_values($userPermissions), 'oauth_token_scopes' => $tokenScopes]);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->token()->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not authenticated.',
        ], 401);
    }
}
