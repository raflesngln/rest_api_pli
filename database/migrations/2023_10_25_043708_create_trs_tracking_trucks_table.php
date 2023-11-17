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
            $table->bigInteger('id_dispatch');
            $table->bigInteger('id_tracking');
            $table->string('created_by');
            $table->imestamp('tracking_date')->nullable();
            $table->string('title');
            $table->float('koli');
            $table->string('pic');
            $table->string('kilometer');
            $table->string('description');
            $table->text('attachment')->nullable();
            $table->enum('is_done',array('0', '1'))->default('0');
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
