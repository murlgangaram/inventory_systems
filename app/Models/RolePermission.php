<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permissions';

    protected $fillable = [
        'role',
        'permission',
        'is_allowed',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'is_allowed' => 'boolean',
        ];
    }
}
