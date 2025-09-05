<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\Log;

class TenantAuthController extends Controller
{
    /**
     * Register a new tenant and user.
     */
    public function register(Request $request)
    {
        // Validations
        $validator = Validator::make($request->all(), [
            'tenant_name' => 'required|string|max:255|unique:tenants,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Validate the incoming request data based on defined rules
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // TODO: Tenant Scope with ID Base
        // $tenantScope = 'tenant:' . $tenant->id;

        // Tenant Basics Scopes
        $tenantScope = ['users.read', 'products.read', 'admin'];

        // Create the tenant
        $tenant = Tenant::create([
            'name' => $request->tenant_name,
            'data' => json_encode(array_merge($request->data ?? [], ['scopes' => $tenantScope])) ?? '[]',
        ]);

        // Create the Domain
        $domain = Domain::create([
            'domain' => $request->data['domain'] ?? strtolower($request->tenant_name) . '.plnl-backend-with-react.test',
            'tenant_id' => $tenant->id,
        ]);

        // Create the User
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->tenant_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'tenant',
            'remember_token' => '',
        ]);

        // Generate Tokens With Scopes
        $token = $user->createToken('Tenant API Token', $tenantScope)->accessToken;

        // Log After SuccessFull Create Tenant
        Log::info('Tenant Created:', $tenant . $user . $domain);

        return response()->json([
            'tenant' => $tenant,
            'user' => $user,
            'domain' => $domain,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }

    /**
     * Login the user and generate an access token.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        $scopes = ['users.read', 'products.read'];

        if ($user->tenant_id) {
            $scopes[] = 'admin';
        }

        $token = $user->createToken('API Token', $scopes)->accessToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }

    /**
     * Get the authenticated user's details.
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Get the authenticated user's permissions.
     */
    public function userpermission(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $permissions = [
            'users.read',
            'users.write',
            'products.read',
            'products.write',
            'view-products',
            'add-products',
            'admin',
        ];

        $userPermissions = [];

        foreach ($permissions as $permission) {
            if ($user->token()->can($permission)) {
                $userPermissions[] = $permission;
            }
        }

        return response()->json([
            'user_permissions' => $userPermissions,
        ]);
    }

    /**
     * Logout and revoke the user's token.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->token()->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not authenticated',
        ], 401);
    }
}
