<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'status',
        'tax',
        'discount',
        'billing_address',
        'billing_country',
        'billing_city',
        'billing_neighborhood',
        'billing_street',
        'billing_building_number',
        'booking_date',
        'total_cost',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'datetime',
    ];


    protected static function booted()
    {
        static::creating(function (Order $order) {
            $now = Carbon::now();
            $max = Order::whereYear('created_at', $now->year)->max('number');
            if (!$max):
                $max = $now->year . '0000';
            endif;
            $order->number = $max + 1;
        });
//        parent::booted(); // TODO: Change the autogenerated stub
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->using(OrderProduct::class);
    }

    protected static function boot()
    {
        parent::boot();
        //Test if it work using Static function and get order
//        static::created(static function ($order) {
//            //dd($order);
//        });
    }

}