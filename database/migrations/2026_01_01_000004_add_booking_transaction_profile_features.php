<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE rooms MODIFY status ENUM('kosong','booking','terisi','maintenance') NOT NULL DEFAULT 'kosong'");

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->enum('status', ['menunggu', 'diterima', 'ditolak', 'dibatalkan'])->default('menunggu');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->string('proof_photo')->nullable()->after('payment_method');
            $table->timestamp('submitted_at')->nullable()->after('proof_photo');
        });

        DB::statement("ALTER TABLE payments MODIFY status ENUM('belum_lunas','menunggu_verifikasi','lunas','ditolak') NOT NULL DEFAULT 'belum_lunas'");

        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'proof_photo', 'submitted_at']);
        });

        Schema::dropIfExists('bookings');

        DB::statement("ALTER TABLE rooms MODIFY status ENUM('kosong','terisi','maintenance') NOT NULL DEFAULT 'kosong'");
        DB::statement("ALTER TABLE payments MODIFY status ENUM('belum_lunas','lunas') NOT NULL DEFAULT 'belum_lunas'");
    }
};
