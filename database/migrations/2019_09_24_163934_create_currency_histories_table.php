<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_histories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
    	    $table->decimal('value', 14, 4);
            $table->string('currency_code');
            $table->date('request_at');
            $table->timestamps();

	    $table->foreign('currency_code')->references('code')->on('currencies')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_histories');
    }
}
