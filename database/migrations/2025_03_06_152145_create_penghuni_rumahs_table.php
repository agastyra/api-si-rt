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
        Schema::create('penghuni_rumahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("penghuni_id")->constrained("penghunis")->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId("rumah_id")->constrained("rumahs")->cascadeOnUpdate()->restrictOnDelete();
            $table->tinyInteger('periode_bulan_mulai')
                ->nullable()
                ->exists([null, 1,2,3,4,5,6,7,8,9,10,11,12]);
            $table->year('periode_tahun_mulai')->nullable();
            $table->tinyInteger('periode_bulan_selesai')
                ->nullable()
                ->exists([null, 1,2,3,4,5,6,7,8,9,10,11,12]);
            $table->year('periode_tahun_selesai')->nullable();
            $table->foreignId("created_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId("updated_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->string('deletion_token')->default('NA');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghuni_rumahs');
    }
};
