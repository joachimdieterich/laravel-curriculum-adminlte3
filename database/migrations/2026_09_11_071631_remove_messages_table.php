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
        // drop deprecated tables
        Schema::dropIfExists('messages');
        Schema::dropIfExists('participants');
        Schema::dropIfExists('threads');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // don't re-create the tables, since we removed the corresponding packages
    }
};