<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDietsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            /*As Ali Shaheen wish */
            $table->text('physical_activities');
//            $table->foreignId('physical_activities_id')->constrained('physical_activities')->cascadeOnDelete();
            $table->enum('gender', [
                'male',
                'female',
                'ذكر',
                'أنثي',
                'أنثى',
            ]);
            $table->enum('status', [
                'on-cart',
                'pending-payment',
                'payment-completed',
                'canceled',
                'completed',
            ])->default('on-cart');
            $table->integer('age');
            $table->unsignedFloat('weight');
            $table->unsignedFloat('height');
            $table->text('chronic_diseases');
            $table->longText('meals_you_like');
            $table->longText('meals_you_dont_like');
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
        Schema::dropIfExists('diets');
    }
}
