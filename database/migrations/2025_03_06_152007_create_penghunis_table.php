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
        Schema::create('penghunis', function (Blueprint $table) {
            $table->id();
            $table->string("nama_lengkap", 100);
            $table->string("foto_ktp", 256);
            $table->enum("status_penghuni", ["Kontrak", "Tetap"]);
            $table->string("nomor_telepon", 13)->unique();
            $table->enum("jenis_kelamin", ["Laki-laki", "Perempuan"]);
            $table->boolean("menikah");
            $table->foreignId("created_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghunis');
    }
};
