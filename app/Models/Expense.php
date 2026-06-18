<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Expense extends Model implements HasMedia
{
    use HasUuids,InteractsWithMedia;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
