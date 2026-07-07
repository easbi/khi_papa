<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('public_links', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('title')->nullable();
            $table->text('activity_ids'); // JSON array of activity ids
            $table->string('created_by_nip')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('public_links');
    }
};
