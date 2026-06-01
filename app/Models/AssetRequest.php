<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class AssetRequest extends Model
{
    use LogsActivity;
    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class,'employee_id','employee_id');
    }

    public function asset(){
        return $this->belongsTo(Asset::class,'asset_id','asset_id');
    }

    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['asset_id', 'employee_id','status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity,string $eventname){

    $status=$this->status;
    $activity->description=$status;

    $activity->properties=[
        'status'=>$status,
        'asset_name'=>$this->asset->name,
        'action_by'=>auth()->user()->name,
    ];
    }

}
