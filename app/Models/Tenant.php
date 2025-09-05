<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'tenants'; 

    protected $fillable = ['name', 'data', 'created_at', 'updated_at'];

    public function users()
    {
        return $this->hasMany(User::class, 'tenant_id');
    }
}
