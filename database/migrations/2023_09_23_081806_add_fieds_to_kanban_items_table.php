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
        Schema::table('kanban_items', function (Blueprint $table) {
            $table->boolean('locked')->default(0);
            $table->boolean('editable')->default(1);
            $table->boolean('visibility')->default(1);
            $table->timestamp('visible_from')->nullable();
            $table->timestamp('visible_until')->nullable();
            $table->string('editors_ids')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kanban_items', function (Blueprint $table) {
            $table->dropColumn('locked');
            $table->dropColumn('editable');
            $table->dropColumn('visibility');
            $table->dropColumn('visible_from');
            $table->dropColumn('visible_until');
            $table->dropColumn('editors_ids');

        });
    }
};
