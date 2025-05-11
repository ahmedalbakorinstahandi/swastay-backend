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
        Schema::create('listing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->boolean('allows_pets')->nullable();
            $table->boolean('allows_smoking')->nullable();
            $table->boolean('allows_parties')->nullable();
            $table->boolean('allows_children')->nullable();
            $table->boolean('remove_shoes')->nullable();
            $table->boolean('no_extra_guests')->nullable();

            $table->json('quiet_hours')->nullable();
            $table->json('restricted_rooms_note')->nullable();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->json('garbage_disposal_note')->nullable();
            $table->json('pool_usage_note')->nullable();
            $table->json('forbidden_activities_note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_rules');
    }
};
