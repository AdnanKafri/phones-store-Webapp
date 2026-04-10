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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'model')) {
                $table->string('model')->nullable()->after('category_id'); // Adjusted after position if brand exists or not logic is complex, just putting it after category_id is safe enough or let it default
            }
            if (!Schema::hasColumn('products', 'location')) {
                $table->string('location')->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'accessories')) {
                $table->text('accessories')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'disassembled_is')) {
                $table->boolean('disassembled_is')->default(false)->after('accessories');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand', 'model', 'location', 'accessories', 'disassembled_is']);
        });
    }
};
