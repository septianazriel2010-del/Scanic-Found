<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->string('claimant_full_name')->nullable();
            $table->string('claimant_class_position', 120)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['claimant_full_name', 'claimant_class_position']);
        });
    }
};