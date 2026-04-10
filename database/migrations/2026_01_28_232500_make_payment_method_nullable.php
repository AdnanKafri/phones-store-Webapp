<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make payment_method nullable and set default for existing rows
        DB::statement('ALTER TABLE payment_requests MODIFY payment_method VARCHAR(255) NULL');
        
        // Update existing rows to have a default value
        DB::table('payment_requests')->whereNull('payment_method')->update(['payment_method' => 'bank_transfer']);
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE payment_requests MODIFY payment_method VARCHAR(255) NOT NULL');
    }
};
