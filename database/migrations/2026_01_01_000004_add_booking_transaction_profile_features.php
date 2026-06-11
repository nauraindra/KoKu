<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite tidak support ALTER TABLE MODIFY ENUM.
        // Pakai string supaya kompatibel dengan SQLite dan MySQL.
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('status')->default('kosong')->change();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('status')->default('menunggu');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->string('proof_photo')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('status')->default('belum_lunas')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'proof_photo', 'submitted_at']);
            $table->string('status')->default('belum_lunas')->change();
        });

        Schema::dropIfExists('bookings');

        Schema::table('rooms', function (Blueprint $table) {
            $table->string('status')->default('kosong')->change();
        });
    }
};
