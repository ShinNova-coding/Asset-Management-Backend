<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class,'employee_id','employee_id');
    }

    public function asset(){
        return $this->belongsTo(Asset::class,'asset_id','asset_id');
    }

}
