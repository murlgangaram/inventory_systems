<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Computer extends Model
{
    use HasFactory;

    protected $table = 'computers';

    protected $fillable = [
        'computer_name',
        'office',
        'ram',
        'cpu',
        'ssd',
        'hd',
        'brand',
        'model',
        'serial_number',
        'mac_address',
        'employee_id',
        'remarks',
        'status',
    ];

    /**
     * A computer belongs to an employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}