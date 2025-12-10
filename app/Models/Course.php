<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'courses';

    /**
     * The primary key for the table.
     */
    protected $primaryKey = 'course_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     * (course_id is increments(), so this stays true)
     */
    public $incrementing = true;

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'int';

    /**
     * Disable timestamps (table has no created_at or updated_at columns)
     */
    public $timestamps = false;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'course_name',
    ];
}
