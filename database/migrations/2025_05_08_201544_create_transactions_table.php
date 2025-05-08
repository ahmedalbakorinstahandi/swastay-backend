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

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->float('amount');
            $table->text('description');
            $table->enum('status', ["pending", "completed", "failed", "refund"]);
            $table->string('type');
            $table->enum('direction', ["in", "out"]);
            $table->enum('method', ["wallet", "shamcash", "alharam", "cash", "crypto"]);
            $table->unsignedBigInteger('transactionable_id')->nullable();
            $table->string('transactionable_type')->nullable();
            $table->string('attached', 100)->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
