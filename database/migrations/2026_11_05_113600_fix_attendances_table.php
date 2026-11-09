<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'attendance_window_id')) {
                $table->unsignedBigInteger('attendance_window_id')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'organisation_id')) {
                $table->unsignedBigInteger('organisation_id')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'student_id')) {
                $table->unsignedBigInteger('student_id')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'subject')) {
                $table->string('subject')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'status')) {
                $table->string('status')->default('present');
            }
            if (!Schema::hasColumn('attendances', 'photo')) {
                $table->string('photo')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'marked_at')) {
                $table->timestamp('marked_at')->nullable();
            }
        });
    }

    public function down(): void
    {
    }
};