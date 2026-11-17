
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printers', function (Blueprint $table) {
            $table->id();

            $table->string('printer_name');
            $table->string('brand_model')->nullable();
            $table->string('serial_number')->nullable()->unique();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('office');

            $table->text('remarks')->nullable();

            $table->enum('status', [
                'Working',
                'For Maintenance',
                'Retired',
                'For Checking'
            ])->default('Working');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
