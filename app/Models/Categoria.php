<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'restaurante_id',
        'nombre',
        'descripcion',
        'posicion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Relación con Restaurante
     */
    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class);
    }

    public function platos()
    {
        return $this->hasMany(Plato::class);
    }
}
