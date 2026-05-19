<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
{
    use LogsActivity;
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

    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'employee_id','status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
