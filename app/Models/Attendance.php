<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'session_id',
        'student_id',
        'name',
        'time_scanned',
        'status',
        'confidence',
        'late_duration',
        'total_outside_time',
        'qr_valid',
        'attendance_date',
    ];
}
