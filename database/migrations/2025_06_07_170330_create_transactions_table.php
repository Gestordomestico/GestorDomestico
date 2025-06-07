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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Clave foránea al usuario que realiza la transacción
            $table->string('type'); // 'income' (ingreso) o 'expense' (gasto)
            $table->decimal('amount', 10, 2); // Monto de la transacción (10 dígitos en total, 2 decimales)
            $table->string('description')->nullable(); // Descripción de la transacción, opcional
            $table->date('date'); // Fecha de la transacción
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};