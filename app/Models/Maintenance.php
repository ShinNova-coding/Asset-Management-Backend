<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Maintenance extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia,HasUuids;

    protected $guarded=[];

    public function asset(){
        return $this->belongsTo(Asset::class,'assets_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'users_id');
    }

    public function category(){
        return $this->belongsTo(Category::class,'categories_id');
     }

    


}
