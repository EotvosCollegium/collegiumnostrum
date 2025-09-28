<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversityFaculty extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
    ];

    public static function universityFacultiesEnum(){
        return self::pluck('name')->toArray();
    }

    public function alumni()
    {
        return $this->belongsToMany(Alumnus::class)->withTimestamps();
    }
}
