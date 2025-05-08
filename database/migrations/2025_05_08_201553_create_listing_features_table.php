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
        Schema::disableForeignKeyConstraints();

        Schema::create('listing_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feature_id');
            $table->foreign('feature_id')->references('id')->on('features');
            $table->unsignedBigInteger('listing_id');
            $table->foreign('listing_id')->references('id')->on('listings');
            $table->timestamp('created_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_features');
    }
};
