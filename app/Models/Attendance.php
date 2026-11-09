<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'organisation_id',
        'student_id',
        'date',
        'time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}