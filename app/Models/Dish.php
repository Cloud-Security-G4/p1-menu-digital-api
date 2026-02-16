<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Dish extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'dishes';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'offer_price',
        'image_url',
        'available',
        'featured',
        'tags',
        'position',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'available' => 'boolean',
        'featured' => 'boolean',
        'tags' => 'array',
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
     * Relación con Categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
