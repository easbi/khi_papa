<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('name');
            }
        });

        $users = DB::table('users')->where(function ($query) {
            $query->whereNull('username')->orWhere('username', '');
        })->get();

        foreach ($users as $user) {
            $fallback = trim((string) ($user->name ?? '')) ?: trim((string) ($user->email ?? ''));
            if ($fallback === '') {
                $fallback = 'user-' . $user->id;
            }

            DB::table('users')->where('id', $user->id)->update([
                'username' => $fallback,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};
