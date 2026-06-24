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
        Schema::table('trpurchaseorderatkhd', function (Blueprint $table) {
            if (!Schema::hasColumn('trpurchaseorderatkhd', 'yayasan_code')) {
                $table->string('yayasan_code', 50)->nullable();
            }
            if (!Schema::hasColumn('trpurchaseorderatkhd', 'yayasan_name')) {
                $table->string('yayasan_name', 100)->nullable();
            }
        });

        Schema::table('trpurchaseorderkitchenhd', function (Blueprint $table) {
            if (!Schema::hasColumn('trpurchaseorderkitchenhd', 'yayasan_code')) {
                $table->string('yayasan_code', 50)->nullable();
            }
            if (!Schema::hasColumn('trpurchaseorderkitchenhd', 'yayasan_name')) {
                $table->string('yayasan_name', 100)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('trpurchaseorderatkhd', function (Blueprint $table) {
            if (Schema::hasColumn('trpurchaseorderatkhd', 'yayasan_code')) {
                $table->dropColumn('yayasan_code');
            }
            if (Schema::hasColumn('trpurchaseorderatkhd', 'yayasan_name')) {
                $table->dropColumn('yayasan_name');
            }
        });

        Schema::table('trpurchaseorderkitchenhd', function (Blueprint $table) {
            if (Schema::hasColumn('trpurchaseorderkitchenhd', 'yayasan_code')) {
                $table->dropColumn('yayasan_code');
            }
            if (Schema::hasColumn('trpurchaseorderkitchenhd', 'yayasan_name')) {
                $table->dropColumn('yayasan_name');
            }
        });
    }
};
