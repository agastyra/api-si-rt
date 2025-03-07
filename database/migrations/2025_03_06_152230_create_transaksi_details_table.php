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
        Schema::create('transaksi_details', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('periode_bulan')
                ->exists([null, 1,2,3,4,5,6,7,8,9,10,11,12]);
            $table->year("periode_tahun");
            $table->foreignId("tipe_transaksi_id")->constrained("tipe_transaksis")->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId("transaksi_id")->constrained("transaksis")->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal("nominal", 10, 2);
            $table->foreignId("created_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId("updated_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->string('deletion_token')->default('NA');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(["id", "deletion_token"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_details');
    }
};
