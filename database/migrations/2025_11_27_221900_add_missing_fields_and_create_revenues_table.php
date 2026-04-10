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
        // 1. Users Table Updates
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('location')->nullable()->after('date_of_birth');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
        });

        // 2. Products Table Updates
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->after('category_id');
            $table->string('model')->after('brand');
            $table->text('defects')->nullable()->after('description');
            $table->text('accessories')->nullable()->after('defects');
            $table->boolean('disassembled_is')->default(false)->after('accessories');
            $table->text('reason_disassembly')->nullable()->after('disassembled_is');
        });

        // 3. Categories Table Updates
        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        // 4. Create Revenues Table
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamp('date');
            $table->enum('status', ['pending', 'completed'])->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand', 'model', 'defects', 'accessories', 'disassembled_is', 'reason_disassembly']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone', 'gender', 'date_of_birth', 'location', 'status']);
        });
    }
};
