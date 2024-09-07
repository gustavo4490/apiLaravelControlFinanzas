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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->string('empresa');
            $table->decimal('cantidad', 10, 2);
            $table->string('detalle');
            $table->unsignedBigInteger('id_tarjeta');
            $table->timestamps();

            // Definir la clave foránea
            $table->foreign('id_tarjeta')->references('id')->on('credit_cards')->onDelete('cascade');

            // Agregar índice para optimizar consultas
            $table->index('id_tarjeta'); // Índice en la columna id_tarjeta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
