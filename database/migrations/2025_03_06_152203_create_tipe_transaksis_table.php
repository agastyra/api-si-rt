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
        Schema::create('tipe_transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->enum('jenis', ['Pemasukan', 'Pengeluaran']);
            $table->foreignId("created_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId("updated_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->string('deletion_token')->default('NA');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(["nama", "deletion_token"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipe_transaksis');
    }
};
