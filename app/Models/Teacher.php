<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    /**
     * Laravel normally expects an auto-incrementing integer "id".
     * But your table uses a string primary key: teacher_id.
     */
    protected $primaryKey = 'teacher_id';
    public $incrementing = false;       // because it's not auto-increment
    protected $keyType = 'string';      // teacher_id is a string

    protected $table = 'teachers';

    /**
     * Fillable columns (mass assignable)
     */
    protected $fillable = [
        'teacher_id',
        'firebase_uid',
        'email',
        'contact_number',
        'firstname',
        'lastname',
        'password',
        'status',
    ];

    /**
     * Disable timestamps because your table only has created_at
     */
    public $timestamps = false;
}
