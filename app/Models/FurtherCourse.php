<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FurtherCourse extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
    ];

    public static function furtherCoursesEnum(){
        return self::pluck('name')->toArray();
    }

    public function alumni()
    {
        return $this->belongsToMany(Alumnus::class)->withTimestamps();
    }
}
