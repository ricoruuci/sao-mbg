<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('msyayasan', function (Blueprint $table) {
            $table->string('yayasan_code', 50)->primary();
            $table->string('yayasan_name', 100);
            $table->string('yayasan_address', 255)->nullable();
            $table->string('yayasan_note', 255)->nullable();
            $table->string('yayasan_phone', 30)->nullable();
            $table->string('yayasan_email', 100)->nullable();
            $table->dateTime('upddate')->nullable();
            $table->string('upduser', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('msyayasan');
    }
};
