<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_activity', function (Blueprint $table) {
            $table->string('berkas_drive_id')->nullable()->after('berkas');
            $table->string('berkas_url')->nullable()->after('berkas_drive_id');
            $table->string('berkas_storage', 10)->default('local')->after('berkas_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['berkas_drive_id', 'berkas_url', 'berkas_storage']);
        });
    }
};
