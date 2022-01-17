<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'service_id',
        'icon_path',
        'order',
    ];

    protected $appends = [
        'icon_url'
    ];

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'service_type_id', 'id');
    }

    public function getIconUrlAttribute()
    {
        if (empty($this->icon_path)):
            /*As Ali Shaheen wish */
            $icon_url = null;
//            $icon_url = 'no icon for this service';
        else:
            $icon_url = url('/uploads/' . $this->icon_path);
        endif;

        return $icon_url;
    }

}
