<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
   
    protected $table = 'students';
    protected $primaryKey = 'student_id';
    public $incrementing = false;      
    protected $keyType = 'string';     
    public $timestamps = false;
    
    protected $fillable = [
        'student_id',
        'firebase_uid',
        'course_id',
        'email',
        'contact_number',
        'firstname',
        'lastname',
        'year',
        'status',
    ];

       protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relationship: Student belongs to a Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
