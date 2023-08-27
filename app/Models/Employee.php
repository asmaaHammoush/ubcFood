<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable,HasRoles;
    protected $table = 'employee';
    public $timestamps=false;
    protected $fillable = [
        'firstName',
        'lastName',
        'middleName',
        'password',
        'email',
        'phoneNum',
        'accountStatus',
        'role',

    ];




//    protected $hidden = [
//        'updated_at',
//        'created_at',
//      //  'password',
//       'remember_token'
//    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class,'roleId','id');
    }
}
