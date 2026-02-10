<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildTag extends Model
{
    protected $table = 'build_tags';

    public $timestamps = false;

    protected $fillable = [
        'build_id',
        'tag_id',
    ];
}
