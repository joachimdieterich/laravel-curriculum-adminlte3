<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferenceSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reference_subscriptions', function (Blueprint $table) {
                    
            $table->char('reference_id');
            $table->string('referenceable_type');
            $table->unsignedbigInteger('referenceable_id');
            
            $table->primary(['reference_id', 'referenceable_type', 'referenceable_id'], 'ref_subscr_ref_id_ref_type_ref_id_primary');
            
            $table->unsignedbigInteger('sharing_level_id');
            $table->boolean('visibility');
            $table->unsignedbigInteger('owner_id');
            $table->timestamps();
            
            $table->foreign('reference_id')->references('id')->on('references');
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
        Schema::dropIfExists('reference_subscriptions');
    }
}
