<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel claims = pengajuan klaim kepemilikan atas sebuah item_report.
     *
     * Relasi:
     * - claims.item_report_id -> item_reports.id (laporan yang diklaim)
     * - claims.claimant_id    -> users.id (yang mengajukan klaim)
     * - claims.reviewed_by    -> users.id (admin yang memverifikasi, nullable)
     *
     * Kolom proof_details bersifat PRIVAT: hanya boleh dilihat oleh
     * claimant itu sendiri dan admin (diatur di ClaimPolicy@view).
     */
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_report_id')
                ->constrained('item_reports')
                ->cascadeOnDelete();

            $table->foreignId('claimant_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Bukti kepemilikan yang ditulis user, contoh: ciri khusus barang,
            // waktu & lokasi kehilangan, dsb. Bersifat privat.
            $table->text('proof_details');

            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])
                ->default('pending')
                ->index();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            // Satu user tidak boleh mengajukan klaim dobel yang masih pending
            // untuk laporan yang sama (dicek juga di level aplikasi/Request).
            $table->index(['item_report_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
