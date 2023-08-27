<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Map extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table='map';
    public $timestamps=false;
    protected $fillable = [
        'latitude',
        'longitude',
        'customerId'
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

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customerId','id');
    }
}
