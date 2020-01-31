<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogbookEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logbook_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('logbook_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('begin');
            $table->timestamp('end')->nullable();
            $table->unsignedbigInteger('owner_id')->nullable();
            $table->timestamps();
            
            $table->foreign('logbook_id')->references('id')->on('logbooks');
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
        Schema::dropIfExists('logbook_entries');
    }
}
