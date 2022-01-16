<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', [
                'on-cart',
                'pending-payment',
                'payment-completed',
                'canceled',
                'completed',
            ])->default('on-cart');
            $table->double('tax')->default(0);
            $table->double('discount')->default(0);
            $table->longText('billing_address')->nullable();
            $table->string('billing_country');
            $table->string('billing_city');
            $table->string('billing_neighborhood');
            $table->string('billing_street');
            $table->string('billing_building_number');
            $table->timestamp('booking_date');
            $table->float('total_cost');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
