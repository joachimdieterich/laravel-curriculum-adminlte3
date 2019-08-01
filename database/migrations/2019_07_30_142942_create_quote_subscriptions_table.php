<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuoteSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_subscriptions', function (Blueprint $table) {
            $table->char('quote_id');
            $table->string('quotable_type');
            $table->unsignedbigInteger('quotable_id');
            
            $table->primary(['quote_id', 'quotable_type', 'quotable_id']);
            
            $table->unsignedbigInteger('sharing_level_id');
            $table->boolean('visibility');
            $table->unsignedbigInteger('owner_id');
            $table->timestamps();
            
            //$table->foreign('quote_id')->references('id')->on('quotes'); //can not be set, sometimes quotes are imported after subscription
            $table->foreign('sharing_level_id')->references('id')->on('sharing_levels');
            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quote_subscriptions');
    }
}
