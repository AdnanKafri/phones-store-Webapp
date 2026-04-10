<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update enum values from matched/closed to approved/rejected
        DB::statement("ALTER TABLE device_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE device_requests MODIFY COLUMN status ENUM('pending', 'matched', 'closed') DEFAULT 'pending'");
    }
};
