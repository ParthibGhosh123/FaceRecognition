<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceWindow extends Model
{
    protected $fillable = [
        'organisation_id',
        'supervisor_id',
        'subject',
        'duration_minutes',
        'is_open',
        'opened_at',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'opened_at' => 'datetime',
    ];
}