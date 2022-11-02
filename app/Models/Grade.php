<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'class_code',
        'teacher_id',
        'class_description'
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }
}



