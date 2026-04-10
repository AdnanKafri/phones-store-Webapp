<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            // Add missing 'type' column
            $table->string('type')->default('deposit')->after('amount');
            // Add missing 'method' column (renamed from payment_method for consistency)
            if (!Schema::hasColumn('payment_requests', 'method')) {
                $table->string('method')->nullable()->after('type');
            }
            // Add proof_image column
            $table->string('proof_image')->nullable()->after('method');
        });
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropColumn(['type', 'method', 'proof_image']);
        });
    }
};
