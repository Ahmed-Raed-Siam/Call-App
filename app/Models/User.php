<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use libphonenumber\PhoneNumberUtil;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'avatar_url',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'role_id',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'user_photo_url','county_code'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * @return BelongsToMany
     */
    public function cartProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'carts')
            ->using(Cart::class)
            ->withPivot(['quantity', 'price'])
            ->as('cart');
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany
     */
    public function diets(): HasMany
    {
        return $this->hasMany(Diet::class);
    }

//    /**
//     * @return BelongsToMany
//     */
//    public function orderProducts(): BelongsToMany
//    {
//        return $this->belongsToMany(Product::class, 'order_products')
//            ->using(OrderProduct::class)
//            ->withPivot(['quantity', 'price'])
//            ->as('order_products');
//    }


    public function routeNotificationForMail($notification = null)
    {
        return $this->email;
    }

    public function routeNotificationForMsegat($notification = null)
    {
        return $this->phone_number;
    }

    public function getUserPhotoUrlAttribute()
    {
        if (empty($this->avatar_url)):
            /*As Ali Shaheen wish */
            $user_photo_url = null;
//        $user_photo_url = 'no photo for this user';
        else:
            $user_photo_url = url('/uploads/' . $this->avatar_url);
        endif;

        return $user_photo_url;
    }

    public function getCountyCodeAttribute()
    {
        $user_phone_number = $this->phone_number;
        $phoneUtil = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneUtil->parse($user_phone_number);
        $CountryCode = $phoneNumber->getCountryCode();

        return $CountryCode;
    }

    /**
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {

//        URL::to('/');
//        $url = 'https://spa.test/reset-password?token=' . $token;
        $url = URL::to('/') . '/reset-password/token=' . $token . '?email=' . $this->email;

        $this->notify(new ResetPasswordNotification($url));
    }

}
