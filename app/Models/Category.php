<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
use HasFactory;

    protected $guarded=[];

    public function asset(){
        return $this->hasMany(Asset::class,'asset_id');
    }
}
