<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentAssignment extends Model
{
    use HasFactory;

    protected $table = 'equipment_assignments';

    protected $fillable = [
        'equipment_type',
        'equipment_id',
        'employee_id',
        'assigned_at',
        'returned_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

   public function computer()
{
    return $this->belongsTo(Computer::class, 'equipment_id');
}

public function printer()
{
    return $this->belongsTo(Printer::class, 'equipment_id');
}   
}
