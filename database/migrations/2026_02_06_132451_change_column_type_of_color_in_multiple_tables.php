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
        Schema::table('curricula', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('terminal_objectives', function (Blueprint $table) {
            $table->char('color', 7)->default('#008000')->change();
        });

         Schema::table('variant_definitions', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('kanbans', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('kanban_statuses', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('kanban_items', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('maps', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('map_marker_categories', function (Blueprint $table) {
            $table->char('color', 7)->change();
        });

        Schema::table('map_marker_types', function (Blueprint $table) {
            $table->char('color', 7)->change();
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('plan_entries', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->char('color', 7)->nullable()->change();
        });

        Schema::table('agenda_item_types', function (Blueprint $table) {
            $table->char('color', 7)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            $table->char('color', 191)->nullable()->default('#3c8dbc99')->change();
        });

        Schema::table('terminal_objectives', function (Blueprint $table) {
            $table->char('color', 9)->default('#008000')->change();
        });

        Schema::table('variant_definitions', function (Blueprint $table) {
            $table->char('color', 191)->nullable()->default('#3c8dbc99')->change();
        });
        
        Schema::table('kanbans', function (Blueprint $table) {
            $table->text('color')->nullable()->change();
        });

        Schema::table('kanban_statuses', function (Blueprint $table) {
            $table->text('color')->nullable()->change();
        });

        Schema::table('kanban_items', function (Blueprint $table) {
            $table->text('color')->nullable()->change();
        });

        Schema::table('maps', function (Blueprint $table) {
            $table->text('color')->nullable()->change();
        });

        Schema::table('map_marker_categories', function (Blueprint $table) {
            $table->string('color')->change();
        });

        Schema::table('map_marker_types', function (Blueprint $table) {
            $table->string('color')->change();
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->text('color')->nullable()->change();
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->text('color')->nullable()->change();
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->string('color')->nullable()->change();
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->text('color')->nullable()->change();
        });

        Schema::table('agenda_item_types', function (Blueprint $table) {
            $table->string('color')->change();
        });
    }
};
