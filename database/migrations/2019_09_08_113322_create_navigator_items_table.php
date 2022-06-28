<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigatorItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigator_items', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->text('title');
            $table->text('description')->nullable();

            $table->unsignedbigInteger('navigator_view_id');

            $table->string('referenceable_type');
            $table->unsignedbigInteger('referenceable_id');
            $table->index(['navigator_view_id', 'referenceable_type', 'referenceable_id'], 'ref_n_v_id_ref_type_ref_id');

            $table->string('position');
            $table->string('css_class');

            $table->boolean('visibility');

            $table->timestamps();

            $table->foreign('navigator_view_id')->references('id')->on('navigator_views');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigator_items');
    }
}
