<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderProduct extends Pivot
{
    use HasFactory;

    protected $fillable = [
//        'user_id',
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $table = 'order_products';

    public $incrementing = false;

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

//    /**
//     * @return BelongsTo
//     */
//    public function user(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }

}
