<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $primaryKey = 'session_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'subject_id',
        'room_id',
        'teacher_id',
        'start_time',
        'end_time',
        'session_days',
        'session_status',
        'qr_code',
        'qr_valid',
        'allowance_time',
    ];

    protected $casts = [
        'session_days' => 'array',
        'qr_valid' => 'boolean',
    ];
}
