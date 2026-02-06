<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideTag extends Model
{
    protected $table = 'guide_tags';

    protected $fillable = [
        'guide_id',
        'tag_id',
    ];
}
