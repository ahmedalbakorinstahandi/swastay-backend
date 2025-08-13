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
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('method', ["wallet", "shamcash", "westernUnion", "alharam", "cash", "office", "crypto", "bank_transfer_euro", "bank_transfer_dollar", "western_union"])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('method', ["wallet", "shamcash", "alharam", "cash", "crypto", "western_union"])->change();
        });
    }
};
