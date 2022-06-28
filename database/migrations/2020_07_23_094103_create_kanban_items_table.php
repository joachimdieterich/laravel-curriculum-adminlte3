<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKanbanItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kanban_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->smallInteger('order_id')->default(0);

            $table->unsignedbigInteger('kanban_id');
            $table->unsignedbigInteger('kanban_status_id');

            $table->unsignedbigInteger('owner_id');
            $table->timestamps();

            $table->foreign('kanban_id')->references('id')->on('kanbans');
            $table->foreign('kanban_status_id')->references('id')->on('kanban_statuses');
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
        Schema::dropIfExists('kanban_items');
    }
}
