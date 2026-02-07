<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuidesVote extends Model
{
    use HasFactory;

    protected $table = 'guides_votes';

    protected $fillable = [
        'user_id',
        'guide_id',
        'tipo'
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }
}
