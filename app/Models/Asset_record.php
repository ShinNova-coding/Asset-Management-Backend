<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset_record extends Model
{
    protected $guarded = [];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'assets_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
