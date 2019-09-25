<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('currency_code_from');
            $table->string('currency_code_to');
            $table->integer('user_id')->unsigned();
    	    $table->decimal('summa', 14, 4);
    	    $table->decimal('value_from', 14, 4);
    	    $table->decimal('value_to', 14, 4);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
	    $table->foreign('currency_code_from')->references('code')->on('currencies')->onUpdate('cascade');
	    $table->foreign('currency_code_to')->references('code')->on('currencies')->onUpdate('cascade');
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
