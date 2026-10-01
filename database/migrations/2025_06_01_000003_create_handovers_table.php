<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel handovers = catatan resmi proses serah terima barang,
     * dibuat SETELAH sebuah claim disetujui (status approved).
     *
     * Relasi:
     * - handovers.claim_id       -> claims.id (satu claim = satu handover)
     * - handovers.handed_over_by -> users.id (admin/staf yang memproses)
     * - handovers.received_by    -> users.id (claimant yang menerima barang)
     */
    public function up(): void
    {
        Schema::create('handovers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('claim_id')
                ->unique() // satu claim hanya boleh punya satu handover
                ->constrained('claims')
                ->cascadeOnDelete();

            $table->foreignId('handed_over_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('received_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('notes')->nullable();
            $table->timestamp('handed_over_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handovers');
    }
};
