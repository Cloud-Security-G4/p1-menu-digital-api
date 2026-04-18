<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'images';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $appends = ['url'];

    public function getUrlAttribute(): ?string
    {
        if (!$this->path) return null;
        $bucket = config('filesystems.disks.gcs.bucket', 'livemenu-images-491400');
        return "https://storage.googleapis.com/{$bucket}/{$this->path}";
    }

    protected $fillable = [
        'restaurant_id',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'thumbnail_path',
        'medium_path',
        'large_path',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }


    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
