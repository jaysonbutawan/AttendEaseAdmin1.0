<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $primaryKey = 'room_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'room_name',
        'building',
        'capacity',
        'floor',
    ];

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class, 'room_id', 'room_id');
    }
}
