<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLmsReferenceSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lms_reference_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('lms_reference_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');

            $table->boolean('editable')->default(0);
            $table->unsignedbigInteger('owner_id');

            $table->timestamps();

            $table->foreign('lms_reference_id')->references('id')->on('lms_references');
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
        Schema::dropIfExists('lms_reference_subscriptions');
    }
}
