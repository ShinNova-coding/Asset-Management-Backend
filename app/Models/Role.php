<?php

namespace App\Models;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
     use HasFactory;

    protected $guarded=[];

    public function user(){
        return $this->belongsToMany(User::class);
    }

   // app/Models/Role.php
public function permission()
{
    return $this->belongsToMany(Permission::class, 'role_has_permission', 'role_id', 'permission_id');
}
}
