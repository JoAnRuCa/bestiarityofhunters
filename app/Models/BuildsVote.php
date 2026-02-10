<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildVote extends Model
{
    use HasFactory;

    protected $table = 'build_votes';

    protected $fillable = [
        'user_id',
        'build_id',
        'tipo'
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function build()
    {
        return $this->belongsTo(Build::class);
    }
}
