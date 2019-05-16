<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            
            $table->unsignedbigInteger('content_id');
            $table->unsignedbigInteger('context_id');
            $table->unsignedbigInteger('sharing_level_id');
            $table->unsignedbigInteger('reference_id');
            $table->unsignedbigInteger('status_id');
            $table->unsignedbigInteger('owner_id');
            
            $table->timestamps();
            
            $table->foreign('content_id')->references('id')->on('contents');
            $table->foreign('context_id')->references('id')->on('contexts');
            //$table->foreign('sharing_level_id')->references('id')->on('sharing_levels');
            //$table->foreign('status_id')->references('status_id')->on('statuses');
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
        Schema::dropIfExists('content_subscriptions');
    }
}
