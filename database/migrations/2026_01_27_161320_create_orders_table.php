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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Buyer
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null'); // For inventory items
            $table->enum('order_type', ['inventory', 'user']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'shipping', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->text('shipping_address')->nullable();
            
            // Approval Workflow
            $table->boolean('seller_approval')->nullable(); // For user marketplace
            $table->boolean('admin_approval')->nullable(); // For both
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
