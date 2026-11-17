<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'employee_id',
        'full_name',
        'position',
        'job_description',
        'office',
        'status',
    ];

    /**
     * An employee can have one account.
     */
    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'employee_id');
    }

    /**
     * An employee can have multiple currently assigned computers.
     */
    public function computers(): HasMany
    {
        return $this->hasMany(Computer::class, 'employee_id');
    }

    /**
     * An employee can have multiple currently assigned printers.
     */
    public function printers(): HasMany
    {
        return $this->hasMany(Printer::class, 'employee_id');
    }

    /**
     * An employee can have multiple current and previous equipment assignments.
     */
    public function equipmentAssignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class, 'employee_id')
            ->orderByDesc('assigned_at');
    }
}
