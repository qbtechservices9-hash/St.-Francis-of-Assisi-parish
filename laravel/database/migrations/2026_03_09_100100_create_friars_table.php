<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->unsignedInteger('served_from_year');
            $table->unsignedInteger('served_to_year')->nullable();
            $table->json('collaborators')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friars');
    }
};
