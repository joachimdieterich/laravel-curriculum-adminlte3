<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerminalObjectiveSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminal_objective_subscriptions', function (Blueprint $table) {
            $table->unsignedbigInteger('terminal_objective_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');
            
            $table->primary(['terminal_objective_id', 'subscribable_type', 'subscribable_id'], 'ter_id_subscr_type_subscr_id_primary');
            
            $table->unsignedbigInteger('sharing_level_id');
            $table->boolean('visibility');
            $table->unsignedbigInteger('owner_id');
            
            $table->timestamps();
            
            $table->foreign('terminal_objective_id')->references('id')->on('terminal_objectives');
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
        Schema::dropIfExists('terminal_objective_subscriptions');
    }
}
