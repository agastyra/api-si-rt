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
            $table->string("nomor_telepon", 13);
            $table->enum("jenis_kelamin", ["Laki-laki", "Perempuan"]);
            $table->boolean("menikah");
            $table->foreignId("created_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId("updated_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->string('deletion_token')->default('NA');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['nomor_telepon', 'deletion_token']);
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
