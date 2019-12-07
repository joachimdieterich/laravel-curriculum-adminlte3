<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationRoleUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_role_users', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedbigInteger('organization_id')->index();
            $table->unsignedbigInteger('user_id')->index();
            $table->unsignedbigInteger('role_id')->index();
            $table->unique(['organization_id', 'user_id']);
            $table->timestamps();
          
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('role_id')->references('id')->on('roles');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_role_users');
    }
}
