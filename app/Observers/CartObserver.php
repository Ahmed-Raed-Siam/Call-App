<?php

namespace App\Observers;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartObserver
{

//    protected $total_price;
//    protected $update_order_status;

    /**
     * @return void
     */
    protected function update_order(): void
    {
        $total_price = 0;
        $user = Auth::user();
        $user_orders = $user->orders();
        $cart_products = $user->cartProducts();
        $user_orders_latest = $user->orders()->latest();
        if ($user_orders->count() > 0 && $cart_products->count() > 0 && $user_orders_latest->first()->status === 'on-cart'):
            foreach ($cart_products->get() as $cart_item):
                $total_price += $cart_item->cart->price;
            endforeach;

            $order = $user_orders_latest->first();
            $order->update(['total_cost' => $total_price]);

//            $this->update_order_status = 1;
//            $this->total_price = $total_price;

        endif;

    }

    /**
     * Handle the Cart "created" event.
     *
     * @param Cart $cart
     * @return void
     */
    public function created(Cart $cart)
    {
        $this->update_order();

//        dd($this->cart());
    }

    /**
     * Handle the Cart "updated" event.
     *
     * @param Cart $cart
     * @return void
     */
    public function updated(Cart $cart)
    {
        $this->update_order();

    }

    /**
     * Handle the Cart "deleted" event.
     *
     * @param Cart $cart
     * @return void
     */
    public function deleted(Cart $cart)
    {
        $this->update_order();
    }

    /**
     * Handle the Cart "restored" event.
     *
     * @param Cart $cart
     * @return void
     */
    public function restored(Cart $cart)
    {
        //
    }

    /**
     * Handle the Cart "force deleted" event.
     *
     * @param Cart $cart
     * @return void
     */
    public function forceDeleted(Cart $cart)
    {
        $this->update_order();
    }
}
