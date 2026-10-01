<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel item_reports = laporan barang hilang ATAU ditemukan.
     * Satu tabel dipakai untuk dua jenis laporan, dibedakan lewat kolom "type".
     * Ini menyederhanakan query search & filter (tidak perlu UNION dua tabel).
     *
     * Relasi:
     * - item_reports.user_id -> users.id (pelapor / reporter)
     */
    public function up(): void
    {
        Schema::create('item_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // "lost" = barang hilang, "found" = barang ditemukan
            $table->enum('type', ['lost', 'found'])->index();

            $table->string('title');
            $table->text('description');
            $table->string('category')->index();
            $table->string('location')->index();
            $table->date('incident_date');

            // Path foto opsional, disimpan di storage/app/public/item-reports
            $table->string('photo_path')->nullable();

            // open     : masih aktif, belum ada klaim yang disetujui
            // claimed  : ada klaim yang sudah di-approve, menunggu serah terima
            // returned : barang sudah diserahkan ke pemilik
            // closed   : laporan ditutup admin (misal salah input / kadaluarsa)
            $table->enum('status', ['open', 'claimed', 'returned', 'closed'])
                ->default('open')
                ->index();

            $table->timestamps();
            $table->softDeletes();

            // Index gabungan untuk filter yang sering dipakai bersamaan
            $table->index(['type', 'status']);
            // Fulltext sederhana untuk pencarian judul+deskripsi (MySQL)
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_reports');
    }
};
