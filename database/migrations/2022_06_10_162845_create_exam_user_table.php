<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_user', function (Blueprint $table) {
            $table->primary(['user_id', 'exam_id']);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('exam_id');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->json('login_data')->nullable();
            $table->boolean('exam_started')->default(false);
            $table->timestamp('exam_completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_user');
    }
}
