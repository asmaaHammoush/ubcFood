<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Order extends Authenticatable
{
    use HasFactory, Notifiable;
   // public $timestamps=true;
    protected $fillable = [
        'code',
        'delivering',
        'orderDate',
        'totalAmount',
        'currentStatus',
        'nextStatus',
        'updated_at',
        'created_at',
    ];

    protected $hidden = [
//        'pivot'

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

//    public function product()
//    {
//
//        return $this->belongsToMany(Warehouse::class,'productorder','orderId','productId','id','id')->withPivot(
//            [
//            'numPieces','pricePeices','rate','orderedQuantity','approvedQuantity','pickedQuantity'
//        ]);
//    }

    public function product()
    {

        return $this->belongsToMany(Product::class,'productorder','orderId','productId','id','id')
            ->withPivot('pricePeices','pickedQuantity','pricePeices','orderedQuantity','approvedQuantity','message');
    }


    public function customer()
    {

        return $this->belongsTo(Customer::class,'customerId','id');
    }


    public function picker()
    {

        return $this->belongsTo(Picker::class,'pickerId','id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouseId','id');
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class,'shippingId','id');
    }


    public function invoice()
    {
        return $this->hasOne(Invoice::class,'orderId','id');
    }
}
