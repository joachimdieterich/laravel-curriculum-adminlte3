<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_designs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->foreignId('curriculum_id')->constrained('curricula')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->longText('document');
            $table->unsignedInteger('revision')->default(1);
            $table->timestamps();
            $table->index(['owner_id', 'organization_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_designs');
    }
};
