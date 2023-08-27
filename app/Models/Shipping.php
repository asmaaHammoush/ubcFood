<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Shipping extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

protected $table='shippings';
    public $timestamps=false;
    protected $fillable = [
        'id',
        'firstName',
        'lastName',
        'email',
        'password',
        'warehouseId',
       'accoutStatus',

    ];


    protected $hidden = [
      //  'password',
      // 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouseId','id');
    }

    public function order()
    {
        return $this->hasMany(Order::class,'shippingId','id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class,'shippingId','id');
    }
}
