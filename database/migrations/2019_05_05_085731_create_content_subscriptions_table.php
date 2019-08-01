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
            
            $table->unsignedbigInteger('content_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');
            
            $table->primary(['content_id', 'subscribable_type', 'subscribable_id'], 'c_subscr_c_id_subscr_type_subscr_id_primary');
            
            $table->unsignedbigInteger('sharing_level_id');
            $table->boolean('visibility');
            $table->unsignedbigInteger('owner_id');
            
            
            $table->timestamps();
            
            $table->foreign('content_id')->references('id')->on('contents');
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
        Schema::dropIfExists('content_subscriptions');
    }
}
