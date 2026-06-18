<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Assignment extends Model
{
    use HasFactory,HasUuids;

    protected $table = 'assignments';

    protected $guarded = [];

    public function asset()
    {
        return $this->belongsTo(Asset::class,'assets_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'users_id');
    }
    
}
