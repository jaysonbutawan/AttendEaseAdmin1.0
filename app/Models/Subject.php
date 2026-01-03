<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $primaryKey = 'subject_id'; 
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'subject_name',
    ];

     public function sessions()
    {
        return $this->hasMany(ClassSession::class, 'subject_id', 'subject_id');
    }
}
