<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Warehouse extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table='warehouse';
    protected $fillable = [
        'name',
        'city',
        'address',
    ];


    protected $hidden = [
        'updated_at',
        'created_at',
//        'pivot'
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

    public function product()
    {
        return $this->belongsToMany(Product::class,'stock','warehouseId','productId','id','id')
            ->withPivot('quantity');
    }

    public function customer()
    {
        return $this->hasMany(Customer::class,'customerId','id');
    }



    public function order()
    {
        return $this->hasMany(Order::class,'warehouseId','id');
    }

    public function picker()
    {
        return $this->hasMany(Picker::class,'warehouseId','id');
    }

    public function shipping()
    {
        return $this->hasMany(Shipping::class,'warehouseId','id');
    }
}
