<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Asset extends Model implements HasMedia
{
    use LogsActivity,InteractsWithMedia,SoftDeletes,HasUuids;
    protected $guarded=[];

   
    public function user(){
        return $this->belongsToMany(User::class,'assignments');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function maintenance(){
        return $this->hasMany(Maintenance::class);
    }

public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['name', 'employee_id', 'status'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(function(string $eventName) {
            if ($eventName === 'updated' && $this->wasChanged('status')) {
                return "Status changed to {$this->status}";
            }
            return "Asset {$eventName}";
        });
}

    public function registerMediaConversions(?Media $media=null):void
    {
        $this->addMediaConversion('preview')//preview loh naming pay call tone poh
             ->fit(Fit::Contain,300,300)//image size
             ->nonQueued();//no wait 
    }

    public function registerMediaCollections():void
    {
        $this->addMediaCollection('images')//call tone poh naming
             ->acceptsMimeTypes(['image/jpeg', 'image/jpg', 'image/png'])//rule tat mark
             ->singleFile();//delete old photos
    }
}
