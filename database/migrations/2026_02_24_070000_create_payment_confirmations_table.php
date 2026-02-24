<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('nama_lengkap', 150);
            $table->string('bank', 50);
            $table->string('nomor_rekening', 50);
            $table->string('bukti_transfer_path', 255);
            $table->text('catatan')->nullable();
            $table->dateTime('confirmed_at');
            $table->boolean('is_read')->default(false)->index();
            $table->dateTime('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_confirmations');
    }
};
