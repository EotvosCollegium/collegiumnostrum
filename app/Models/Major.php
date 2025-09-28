<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    public static function majorsEnum()
    {
        return self::where('show_in_enums', true)->pluck('name')->toArray();
    }

    protected $fillable=[
        'name',
        'show_in_enums',
    ];

    public function alumni()
    {
        return $this->belongsToMany(Alumnus::class)->withTimestamps();
    }
}
