<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedGuide extends Model
{
    use HasFactory;
    public function guide()
{
    return $this->belongsTo(Guide::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
}
