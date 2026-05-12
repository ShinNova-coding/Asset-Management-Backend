<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    /** @use HasFactory<\Database\Factories\AssetsFactory> */
    use HasFactory;
    protected $guarded=[];

    protected $primaryKey='asset_id';//primary key change

    public $incrementing=false;//no auto-inc

    protected $keyType='string';

    public function user(){
        return $this->belongsToMany(User::class,'assignments','employee_id','asset_id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function maintenance(){
        return $this->hasMany(Maintenance::class);
    }

    
}
