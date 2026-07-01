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
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'start_date')) {
                $table->date('start_date')->nullable()->after('type');
            }

            if (!Schema::hasColumn('notifications', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            if (!Schema::hasColumn('notifications', 'image_path')) {
                $table->string('image_path')->nullable()->after('end_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'image_path')) {
                $table->dropColumn('image_path');
            }

            if (Schema::hasColumn('notifications', 'end_date')) {
                $table->dropColumn('end_date');
            }

            if (Schema::hasColumn('notifications', 'start_date')) {
                $table->dropColumn('start_date');
            }
        });
    }
};
