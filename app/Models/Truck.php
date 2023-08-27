<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Truck extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table='trucks';
    public $timestamps=false;
    protected $fillable = [
        'latitude',
        'longitude',
        'shippingId'
    ];


    protected $hidden = [
//        'updated_at',
//        'created_at',
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

    public function shipping()
    {
        return $this->hasMany(Shipping::class,'shippingId','id');
    }
}
