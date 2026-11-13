<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->boolean('is_master')->default(false));
        // The project owner's existing account is the protected master account.
        DB::table('users')->whereRaw('LOWER(email) = ?', ['tomislav@agmedia.hr'])
            ->where('is_admin', true)->update(['is_master' => true]);
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_master'));
    }
};
