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
        Schema::table('kanban_subscriptions', function (Blueprint $table) {
            $table->text('sharing_token')->nullable();
            $table->text('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kanban_subscriptions', function (Blueprint $table) {
            $table->dropColumn('sharing_token');
            $table->dropColumn('title');
        });
    }
};
