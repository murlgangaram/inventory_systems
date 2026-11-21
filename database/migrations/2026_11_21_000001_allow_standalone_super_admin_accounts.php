<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->unsignedBigInteger('employee_id')->nullable()->change();
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::table('accounts')->whereNull('employee_id')->exists()) {
            throw new RuntimeException('Cannot roll back: link all standalone accounts to employees first.');
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });
    }
};
