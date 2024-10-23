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
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('saldo', 10, 2);
            $table->string('icono');
            $table->unsignedBigInteger('idusuario');
            $table->string('tipo');
            $table->timestamps();
            // Definir la clave foránea
            $table->foreign('idusuario')->references('id')->on('users')->onDelete('cascade');

            // Agregar índice para optimizar consultas
            $table->index('idusuario'); // Índice en la columna idusuario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
