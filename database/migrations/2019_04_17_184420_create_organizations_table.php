<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('common_name')->nullable(); // external key
            $table->string('title'); //newer version of institution
            $table->text('description')->nullable();

            $table->string('street')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            
            $table->char('state_id')->default('DE-RP');
            $table->char('country_id')->default('DE');
            
            $table->unsignedBigInteger('organization_type_id')->default(1);
            
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->unsignedbigInteger('status_id')->default(2); // newer version of confirmed // 2 == pending activation

            $table->timestamps();
            
            $table->foreign('state_id')->references('code')->on('states');
            $table->foreign('country_id')->references('alpha2')->on('countries');
            $table->foreign('organization_type_id')->references('id')->on('organization_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
