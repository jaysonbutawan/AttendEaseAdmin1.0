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
        'session_date',
        'session_status',
        'qr_code',
        'qr_valid',
        'allowance_time',
    ];

    protected $casts = [
        'session_date' => 'date',
        'qr_valid' => 'boolean',
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }
}
