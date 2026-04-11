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
        Schema::create('timebooking_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('timebooking_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('capacity')->default(1);
            $table->integer('available_capacity')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['start_time', 'end_time']);
            $table->index(['category_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timebooking_appointments');
    }
};