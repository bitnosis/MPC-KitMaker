<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->timestamp('entered_trade')->nullable();
            $table->timestamp('exited_trade')->nullable();
            $table->integer('amount');
            $table->float('enter_price');
            $table->float('exit_price');
            $table->string('symbol');
            $table->float('profit_loss');
            $table->float('commission');
            $table->string('direction');
            $table->string('market_type');
            $table->string('transaction_id');
            $table->text('notes');
            $table->integer('image_id');
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
        Schema::dropIfExists('trades');
    }
}
