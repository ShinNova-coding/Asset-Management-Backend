<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Maintenance extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $guarded=[];

    public function asset(){
        return $this->belongsTo(Asset::class,'asset_id','asset_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'employee_id','employee_id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

}
