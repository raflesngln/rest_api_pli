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
  Schema::create('ms_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('driver_no');
            $table->string('driver_name');
            $table->string('driver_contact_number1')->nullable();
            $table->string('driver_contact_number2')->nullable();
            $table->enum('is_active',array('0', '1'))->default('0');
            $table->enum('is_deleted',array('0', '1'))->default('0');
            $table->string('ip')->nullable();
            $table->string('create_by')->nullable();
            $table->string('vendor_id')->nullable();
            $table->string('email');
            $table->string('password');
            $table->string('email_verified_at');
            $table->string('remember_token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_drivers');
    }
};
