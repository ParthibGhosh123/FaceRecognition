<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_windows', function (Blueprint $table) {
            $table->foreignId('organisation_id')->nullable()->after('id')->constrained('organisations')->onDelete('cascade');
            $table->foreignId('supervisor_id')->nullable()->after('organisation_id')->constrained('users')->onDelete('cascade');
            $table->string('subject')->nullable()->after('supervisor_id');
            $table->integer('duration_minutes')->default(10)->after('subject');
            $table->boolean('is_open')->default(true)->after('duration_minutes');
            $table->timestamp('opened_at')->nullable()->after('is_open');
            $table->timestamp('closed_at')->nullable()->after('opened_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_windows', function (Blueprint $table) {
            $table->dropColumn(['organisation_id','supervisor_id','subject','duration_minutes','is_open','opened_at','closed_at']);
        });
    }
};