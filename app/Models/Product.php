<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Product extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table='product';
    public $timestamps=true;
    protected $fillable = [
        'name',
        'image',
        'weight',
        'price',
        'available',
        'description',
        'categorytId',
        'code'
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


    public function category()
    {
        return $this->belongsTo(Category::class,'categorytId','id');
    }


    public function warehouse()
    {
        return $this->belongsToMany(Warehouse::class,'stock','productId','warehouseId','id','id')
            ->withPivot('quantity');
    }

    public function order()
    {
        return $this->belongsToMany(Warehouse::class,'productorder','productId','orderId','id','id');
    }


}
