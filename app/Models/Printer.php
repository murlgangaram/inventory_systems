<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Printer extends Model
{
    use HasFactory;

    protected $table = 'printers';

    protected $fillable = [
        'printer_name',
        'brand_model',
        'serial_number',
        'employee_id',
        'office',
        'remarks',
        'status',
    ];

    /**
     * A printer belongs to an employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * A printer can have multiple assignment history records.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class, 'equipment_id')
            ->where('equipment_type', 'printer')
            ->orderByDesc('assigned_at');
    }
}
