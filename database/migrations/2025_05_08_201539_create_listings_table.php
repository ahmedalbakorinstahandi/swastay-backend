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

        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('host_id');
            $table->foreign('host_id')->references('id')->on('users');
            $table->text('title');
            $table->text('description');
            $table->unsignedBigInteger('house_type_id');
            $table->foreign('house_type_id')->references('id')->on('house_types');
            $table->enum('property_type', ["House", "Apartment", "Guesthouse"]);
            $table->float('price');
            $table->string('currency', 5)->default('SYP');
            $table->float('commission');
            $table->float('service_fees')->nullable();
            $table->enum('status', ["draft", "in_review", "approved", "paused", "archived"]);
            $table->tinyInteger('guests');
            $table->tinyInteger('bedrooms');
            $table->integer('beds');
            $table->float('bathrooms');
            $table->integer('booking_capacity');
            $table->boolean('is_contains_cameras')->default(false);
            $table->string('camera_locations', 350)->nullable();
            $table->boolean('noise_monitoring_device');
            $table->boolean('weapons_on_property');
            $table->tinyInteger('floor_number')->default(1);
            $table->integer('min_booking_days')->default(1);
            $table->bigInteger('max_booking_days')->default(730);
            $table->time('check_in');
            $table->time('check_out');
            $table->text('rules_content')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
