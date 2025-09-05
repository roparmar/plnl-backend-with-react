<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class TenantsAndProductsSeeder extends Seeder
{
    public function run()
    {
        $tenant = Tenant::create([
            'id' => '1',
            'name' => 'Acme Corp',
            'data' => json_encode(['custom_data' => 'value']),
        ]);

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@acme.com',
            'password' => Hash::make('password123'),
            'tenant_id' => $tenant->id,
        ]);

        $token = $user->createToken('Tenant API Token', ['users.read', 'products.read', 'admin'])->accessToken;

        Product::create([
            'name' => 'Product 1',
            'description' => 'Description for Product 1',
            'tenant_id' => $tenant->id,
            'price' => 100.00,
        ]);

        Product::create([
            'name' => 'Product 2',
            'description' => 'Description for Product 2',
            'tenant_id' => $tenant->id,
            'price' => 150.00,
        ]);
    }
}
