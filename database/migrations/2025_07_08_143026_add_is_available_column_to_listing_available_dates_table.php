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
        Schema::table('listing_available_dates', function (Blueprint $table) {
            $table->boolean('is_available')->after('available_date')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_available_dates', function (Blueprint $table) {
            $table->dropColumn('is_available');
        });
    }
};
