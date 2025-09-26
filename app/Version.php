<?php

namespace App;

class Version
{
    public static function isNostrum(){
        return config('app.version') === "nostrum";
    }

    public static function isHellas(){
        return config('app.version') === "hellas";
    }
}
