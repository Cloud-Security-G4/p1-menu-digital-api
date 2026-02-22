<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'restaurants';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
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
            $restaurant->slug = self::createAnUniqueRestautantSlug($restaurant->name);

            if (isset($restaurant->hours)) {
                self::validateHoursFormat($restaurant->hours);
            }
        });

        static::updating(function ($restaurant) {
            if ($restaurant->isDirty('name')) {
                $restaurant->slug = self::createAnUniqueRestautantSlug($restaurant->name);
            }

            if ($restaurant->isDirty('hours')) {
                self::validateHoursFormat($restaurant->hours);
            }
        });
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    private static function createAnUniqueRestautantSlug($name): string
    {
        $slug = Str::slug($name);
        $count = self::withTrashed()->where('slug', 'LIKE', "{$slug}%")->count();

        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        return $slug;
    }

    private static function validateHoursFormat(array $hours): void
    {
        $days = [
            'monday', 'tuesday', 'wednesday',
            'thursday', 'friday', 'saturday', 'sunday'
        ];

        foreach ($hours as $day => $ranges) {

            if (! in_array($day, $days, true)) {
                throw new \InvalidArgumentException("Día inválido: {$day}");
            }

            if (! is_array($ranges)) {
                throw new \InvalidArgumentException("El valor de {$day} debe ser un array");
            }

            foreach ($ranges as $index => $range) {

                if (! is_array($range)) {
                    throw new \InvalidArgumentException("Formato inválido en {$day}[{$index}]");
                }

                if (! isset($range['open'], $range['close'])) {
                    throw new \InvalidArgumentException(
                        "Cada rango en {$day} debe tener 'open' y 'close'"
                    );
                }

                if (! self::isValidTime($range['open']) || ! self::isValidTime($range['close'])) {
                    throw new \InvalidArgumentException(
                        "Formato de hora inválido en {$day} (HH:MM)"
                    );
                }

                if ($range['open'] >= $range['close']) {
                    throw new \InvalidArgumentException(
                        "La hora de apertura debe ser menor que la de cierre en {$day}"
                    );
                }
            }
        }
    }
    private static function isValidTime(string $time): bool
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time) === 1;
    }
}
