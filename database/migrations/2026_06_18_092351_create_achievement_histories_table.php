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
        Schema::create('achievement_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_id')->constrained();
            $table->char('status', 2);
            $table->unsignedBigInteger('owner_id'); // user who set status for this entry
            // updated_at is not needed, since these entries shouldn't be updated
            $table->timestamp('created_at')->nullable();

            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievement_histories');
    }
};
