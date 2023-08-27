<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    protected $table = 'customer';
   // public $timestamps=true;
    protected $fillable = [
        'id',
        'firstName',
        'lastName',
        'date',
        'password',
        'email',
        'phoneNum',
        'latitude',
        'latitude',
        'accountStatus',
        'status',
        'paymentMethod',
        'warehouseId',
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


    public function order()
    {
        return $this->hasMany(Order::class,'customerId','id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouseId','id');
    }


    public function map()
    {
        return $this->belongsTo(Map::class,'customerId','id');
    }
}
