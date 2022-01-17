<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mockery\Exception;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'service_type_id',
        'image',
        'order',
        'cost',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'service_type_name', 'full_name', 'image_url'
    ];

    protected static function boot()
    {
        parent::boot();

        /*static::addGlobalScope('vip', function(Builder $builder) {
            return $builder->where('price', '>=', 300);
        });*/

//        static::deleting(function ($model) {
//            $count = OrderProduct::where('product_id', $model->id)->count();
//            if ($count > 0) {
//                throw new Exception('Cannot delete product has orders!');
//            }
//        });
    }

    /**
     * @return BelongsTo
     */
    public function service_type(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'id')
            ->withDefault([
                'name' => 'No Service Type',
            ]);
    }

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * @return BelongsToMany
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->using(OrderProduct::class);
    }


    /**
     * @return string
     */
    public function getServiceTypeNameAttribute(): string
    {
        //        dd(
//            $service_type,
//            $service_type->name,
//        );
        return ServiceType::find($this->service_type_id)->name;
    }

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ' ' . $this->cost;
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->image)):
            /*As Ali Shaheen wish */
            $image_url = null;
//            $image_url = 'no image for this product';
        else:
            $image_url = url('/uploads/' . $this->image);
        endif;

        return $image_url;
    }

}
