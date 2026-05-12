<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $guarded=[];

    // app/Models/Permission.php
public function role()
{
    return $this->belongsToMany(Role::class, 'role_has_permission', 'permission_id', 'role_id');
}
    
}
