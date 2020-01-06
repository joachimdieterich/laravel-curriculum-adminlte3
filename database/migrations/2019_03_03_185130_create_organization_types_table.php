<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_types', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('title');
            $table->integer('external_id')->unsigned();
            
            $table->char('state_id')->nullable();
            $table->char('country_id');
            
            $table->timestamp('created_at')->nullable();
            
            //$table->foreign('state_id')->references('code')->on('states');
            $table->foreign('country_id')->references('alpha2')->on('countries');     
             
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists('organization_types');
    }
}
