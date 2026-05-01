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
        Schema::table('msbahanbaku', function (Blueprint $table) {
            $table->char('fg_active', 1)->default('Y')->after('fgform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('msbahanbaku', function (Blueprint $table) {
            $table->dropColumn('fg_active');
        });
    }
};
