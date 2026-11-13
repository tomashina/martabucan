<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email')->unique();
            $table->string('password'); $table->boolean('is_admin')->default(false);
            $table->rememberToken(); $table->timestamps();
        });
        Schema::create('site_contents', function (Blueprint $table) {
            $table->id(); $table->string('key')->unique(); $table->json('value'); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('site_contents'); Schema::dropIfExists('users'); }
};
