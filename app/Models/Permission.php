<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Permission extends Authenticatable
{
    use HasFactory, Notifiable;
   // public $timestamps=true;
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

    public function role()
    {
        return $this->belongsToMany(Role::class,'role_has_permissions','permission_id','role_id','id','id');
    }
}
