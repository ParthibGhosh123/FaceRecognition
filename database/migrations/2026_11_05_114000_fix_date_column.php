<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // date column ke nullable kore dao, tahole ar error asbe na
        DB::statement("ALTER TABLE attendances MODIFY date DATE NULL");

        // Jodi date column na thake, tahole baniye dao
        if (!Schema::hasColumn('attendances', 'date')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->date('date')->nullable();
            });
        }
    }

    public function down(): void
    {
    }
};