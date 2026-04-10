<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change 'status' column from ENUM to VARCHAR(20) to prevent truncation
        // and support values like 'pending', 'rejected', etc.
        Schema::table('products', function (Blueprint $table) {
            // altering enum to string using raw SQL for safety and compatibility
            // MySQL syntax
            DB::statement("ALTER TABLE products MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'available'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM if needed (not recommended to lose data, but for completeness)
        // We will keep it as string in rollback or try to revert to enum
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('available', 'sold', 'hidden') DEFAULT 'available'");
    }
};
