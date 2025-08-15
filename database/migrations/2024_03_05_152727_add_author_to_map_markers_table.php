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
        Schema::table('map_markers', function (Blueprint $table) {
            $table->string('author')->nullable()->after('description');
            $table->string('url_title')->nullable()->after('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('map_markers', function (Blueprint $table) {
            $table->dropColumn('author');
            $table->dropColumn('url_title');
        });
    }
};
