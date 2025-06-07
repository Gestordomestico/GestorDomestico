<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Columnas que pueden ser asignadas masivamente
    protected $fillable = [
        'user_id',
		'category_id', // <-- Añade esta línea
        'type',
        'amount',
        'description',
        'date',
    ];

    // Definir la relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Nueva relación: Una transacción pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }	
}