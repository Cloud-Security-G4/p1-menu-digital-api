<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    protected $table = 'restaurants';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'phone',
        'address',
        'hours',
    ];

    protected $casts = [
        'hours' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($restaurant) {
            $restaurant->id = (string) Str::uuid();
            $restaurant->slug = Str::slug($restaurant->name);
        });
    }
    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }
}
