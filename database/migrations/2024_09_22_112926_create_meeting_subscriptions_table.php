<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meeting_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('meeting_id');
            $table->string('subscribable_type');
            $table->unsignedbigInteger('subscribable_id');

            $table->smallInteger('order_id')->default(0);
            $table->boolean('editable')->default(0);
            $table->unsignedbigInteger('owner_id');

            $table->date('due_date')->nullable();
            $table->text('sharing_token')->nullable();
            $table->text('title')->nullable();

            $table->timestamps();

            $table->foreign('meeting_id')->references('id')->on('meetings');
            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_subscriptions');
    }
};
