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
        Schema::table('kanban_statuses', function (Blueprint $table) {
            $table->boolean('editable')->default(1);
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
        Schema::table('kanban_statuses', function (Blueprint $table) {
            $table->dropColumn('editable');
            $table->dropColumn('visible_from');
            $table->dropColumn('visible_until');
            $table->dropColumn('editors_ids');
        });
    }
};
