<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->integer('quantity')->default(1);
            $table->integer('price');
            $table->string('status'); // Status can be 'packed', 'shipped', 'delivered', 'canceled', etc.
            $table->unsignedBigInteger('products_id');
            $table->unsignedBigInteger('address_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("products_id")->references("id")->on("products")->onDelete("cascade");
            $table->foreign("address_id")->references("id")->on("addresses")->onDelete("cascade");
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
};
