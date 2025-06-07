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
        Schema::table('transactions', function (Blueprint $table) {
            // Asegúrate de que la columna category_id se añade después de user_id si es posible.
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null')->after('user_id');
            // `nullable()`: Permite que las transacciones existan sin categoría (ej. si la categoría se elimina).
            // `onDelete('set null')`: Si la categoría asociada se elimina, establece `category_id` de la transacción a NULL.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['category_id']); // Elimina la clave foránea primero
            $table->dropColumn('category_id'); // Luego elimina la columna
        });
    }
};