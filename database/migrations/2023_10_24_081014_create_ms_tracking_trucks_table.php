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
        Schema::create('ms_tracking_trucks', function (Blueprint $table) {
            $table->id();
            $table->integer('sorting');
            $table->string('title');
            $table->string('description')->nullable();
            $table->enum('is_active',array('0', '1'))->default('1');
            $table->enum('is_deleted',array('0', '1'))->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_tracking_trucks');
    }
};
