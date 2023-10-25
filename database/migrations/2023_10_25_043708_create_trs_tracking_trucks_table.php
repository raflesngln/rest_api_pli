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
        Schema::create('trs_tracking_trucks', function (Blueprint $table) {
            $table->id();
            $table->integer('tracking_date');
            $table->string('title');
            $table->string('qty');
            $table->string('PIC');
            $table->string('estimate_pickup');
            $table->string('description');
            $table->string('attachment')->nullable();
            $table->enum('is_active',array('0', '1'))->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trs_tracking_trucks');
    }
};
