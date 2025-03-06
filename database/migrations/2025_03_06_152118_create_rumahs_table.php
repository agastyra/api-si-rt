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
        Schema::create('rumahs', function (Blueprint $table) {
            $table->id();
            $table->string("blok", 5);
            $table->enum("status_rumah", ["Dihuni", "Tidak dihuni"]);
            $table->foreignId("created_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId("updated_by")->constrained("users")->cascadeOnUpdate()->restrictOnDelete();
            $table->string('deletion_token')->default('NA');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(["blok", "deletion_token"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rumahs');
    }
};
