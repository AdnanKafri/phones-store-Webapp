<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model_name');
            $table->string('slug')->unique();
            $table->string('image_url')->nullable();
            $table->unsignedSmallInteger('release_year')->nullable();
            $table->string('battery')->nullable();
            $table->string('camera')->nullable();
            $table->string('storage')->nullable();
            $table->string('ram')->nullable();
            $table->string('processor')->nullable();
            $table->string('performance')->nullable();
            $table->string('display')->nullable();
            $table->string('operating_system')->nullable();
            $table->json('specs')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
