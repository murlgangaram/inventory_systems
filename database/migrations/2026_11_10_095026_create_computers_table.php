
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('computers', function (Blueprint $table) {
            $table->id();

            $table->string('computer_name')->unique();
            $table->string('office');
            $table->string('ram')->nullable();
            $table->string('cpu')->nullable();
            $table->string('ssd')->nullable();
            $table->string('hd')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('mac_address')->nullable();
            
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

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
        Schema::dropIfExists('computers');
    }
};
