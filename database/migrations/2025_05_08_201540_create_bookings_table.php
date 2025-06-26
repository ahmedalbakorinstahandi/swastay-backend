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

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->foreign('listing_id')->references('id')->on('listings');
            $table->unsignedBigInteger('host_id');
            $table->foreign('host_id')->references('id')->on('users');
            $table->unsignedBigInteger('guest_id');
            $table->foreign('guest_id')->references('id')->on('users');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['pending', 'accepted', 'confirmed', 'completed', 'cancelled', 'rejected']);
            $table->string('currency', 5)->default('USD');
            $table->float('price');
            $table->float('commission')->default(0);
            $table->float('service_fees')->nullable();
            $table->text('message')->nullable();
            $table->bigInteger('adults_count');
            $table->bigInteger('children_count');
            $table->bigInteger('infants_count');
            $table->bigInteger('pets_count');
            $table->text('host_notes')->nullable();
            $table->text('admin_notes')->nullable();
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
        Schema::dropIfExists('bookings');
    }
};
