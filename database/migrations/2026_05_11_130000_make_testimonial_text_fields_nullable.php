<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->text('content')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->text('content')->nullable(false)->change();
        });
    }
};
