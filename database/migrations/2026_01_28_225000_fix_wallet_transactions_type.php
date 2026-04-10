<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Change enum to string to support 'deposit', 'withdraw', 'purchase', 'sale', etc.
            $table->string('type')->change();
        });
    }

    public function down(): void
    {
        // Revert not strictly needed for this fix, but good practice
        // Schema::table('wallet_transactions', function (Blueprint $table) {
        //    $table->enum('type', ['credit', 'debit'])->change();
        // });
    }
};
