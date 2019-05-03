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
            $table->bigIncrements('id');
            $table->bigInteger('organization_id')->index();
            $table->bigInteger('user_id')->index();
            $table->string('role_id')->index();
            $table->unique(['organization_id', 'user_id']);
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
        Schema::dropIfExists('organization_role_users');
    }
}
