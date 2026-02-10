<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function guides()
    {
        return $this->belongsToMany(Guide::class, 'guide_tags');
    }

    public function builds()
    {
        return $this->belongsToMany(Build::class, 'build_tags');
    }
}
