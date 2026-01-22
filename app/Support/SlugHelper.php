<?php

namespace App\Support;

use Illuminate\Support\Str;

class SlugHelper
{
    public static function make(string $text): string
    {
        return Str::slug($text);
    }
}
