<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Role extends Authenticatable
{
    use HasFactory, Notifiable;
    public $timestamps=false;
    protected $fillable = [
         'name',
        'updated_at',
        'created_at',
    ];




//    protected $hidden = [
//        'updated_at',
//        'created_at',
//      //  'password',
//       //'remember_token'
//    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    public function permission()
    {
        return $this->belongsToMany(Permission::class,'role_has_permissions','role_id','permission_id','id','id');
    }

    public function employee()
    {
        return $this->hasMany(Employee::class,'roleId','id');
    }
}
