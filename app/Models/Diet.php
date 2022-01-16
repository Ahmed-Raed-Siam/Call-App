<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        /*As Ali Shaheen wish */
        'physical_activities',
//        'physical_activities_id',
        'gender',
        'status',
        'age',
        'weight',
        'height',
        'chronic_diseases',
        'meals_you_like',
        'meals_you_dont_like',
    ];


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
