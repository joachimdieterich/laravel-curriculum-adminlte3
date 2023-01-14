<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kanban_item_comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment');
            $table->unsignedbigInteger('kanban_item_id');
            $table->unsignedbigInteger('user_id');
            $table->timestamps();

            $table->foreign('kanban_item_id')->references('id')->on('kanban_items')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kanban_item_comment');
    }
};
