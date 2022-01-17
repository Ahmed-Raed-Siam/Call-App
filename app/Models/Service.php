<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon_path',
        'order',
    ];

    protected $appends = [
        'icon_url'
    ];


    /**
     * @return HasMany
     */
    public function service_types(): HasMany
    {
        return $this->hasMany(ServiceType::class, 'service_id');
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
