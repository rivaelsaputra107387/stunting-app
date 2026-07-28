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
        Schema::create('pemeriksaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balitas')->cascadeOnDelete();
            $table->foreignId('posyandu_id')->constrained('posyandus')->cascadeOnDelete();
            $table->date('tanggal_pemeriksaan');
            $table->unsignedSmallInteger('umur_bulan');
            $table->decimal('tinggi_badan', 5, 2);
            $table->decimal('berat_badan', 5, 2);
            $table->enum('status_stunting', ['Normal', 'Risk of Stunting', 'Stunting'])->nullable();
            $table->decimal('zscore_tb_u', 5, 2)->nullable();
            $table->enum('status_berat_badan', ['Sangat Kurang', 'Kurang', 'Normal', 'Risiko Berat Badan Lebih'])->nullable();
            $table->decimal('zscore_bb_u', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Prevent duplicate examination per balita per date
            $table->unique(['balita_id', 'tanggal_pemeriksaan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaans');
    }
};
