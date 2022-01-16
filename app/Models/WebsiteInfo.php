<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class WebsiteInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'name',
        'phone_number',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'logo_url',
        'whats_app_number_url',
    ];

    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)):
            $logo_url = 'no image for this logo';
        else:
            $logo_url = url('/uploads/' . $this->logo);
        endif;

        return $logo_url;
    }

    /**
     * @return string
     */
    public function getWhatsAppNumberUrlAttribute(): string
    {

        return URL::to('https://wa.me/' . $this->phone_number);
    }

}
