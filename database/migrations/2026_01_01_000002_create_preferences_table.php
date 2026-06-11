<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->unique()->constrained()->cascadeOnDelete();
            $table->enum('theme', ['light', 'dark'])->default('light');
            $table->string('accent_color')->default('#ec4899');
            $table->string('weather_city')->default('Jakarta');
            $table->decimal('latitude', 10, 6)->default(-6.200000);
            $table->decimal('longitude', 10, 6)->default(106.816666);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preferences');
    }
};
