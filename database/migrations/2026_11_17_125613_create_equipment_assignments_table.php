<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_assignments', function (Blueprint $table) {
            $table->id();

            // Equipment identification
            $table->enum('equipment_type', ['computer', 'printer']);
            $table->unsignedBigInteger('equipment_id');

            // Assigned employee
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete();

            // Assignment dates
            $table->timestamp('assigned_at');
            $table->timestamp('returned_at')->nullable();

            // Assignment notes
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(
                ['equipment_type', 'equipment_id'],
                'equipment_assignments_equipment_index'
            );

            $table->index(
                ['employee_id', 'returned_at'],
                'equipment_assignments_employee_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_assignments');
    }
};
