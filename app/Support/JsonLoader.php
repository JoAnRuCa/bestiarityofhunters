<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class JsonLoader
{
    public static function load(string $path): array
    {
        return json_decode(Storage::get($path), true);
    }
}
