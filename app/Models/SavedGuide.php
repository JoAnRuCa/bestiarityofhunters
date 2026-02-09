<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedGuide extends Model
{
    // Esto permite que el método SavedGuide::create([...]) funcione
    protected $fillable = ['user_id', 'guide_id'];

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }
}
