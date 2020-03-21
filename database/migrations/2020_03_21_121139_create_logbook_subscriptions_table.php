<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogbookSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logbook_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedbigInteger('logbook_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');
            
            $table->unsignedbigInteger('owner_id');
            $table->timestamps();
            
            $table->foreign('logbook_id')->references('id')->on('logbooks');
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
        Schema::dropIfExists('logbook_subscriptions');
    }
}
