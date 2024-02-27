<?php

namespace App\Models;

use App\Services\Contract\FileStorageServiceContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory, Sortable;

    protected $table = 'products';

    protected $fillable = [
        'slug',
        'directory',
        'title',
        'description',
        'SKU',
        'price',
        'new_price',
        'quantity',
        'thumbnail',
    ];

    protected $hidden = [];

    protected $casts = [];

    public $sortable = [
        'id',
        'title',
        'SKU',
        'price',
        'quantity',
        'created_at',
        'updated_at',
    ];

    public $sortableAs = ['categories_count'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0);
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (Storage::has($this->attributes['thumbnail'])) {
                    return Storage::url($this->attributes['thumbnail']);
                }

                return $this->attributes['thumbnail'];
            }
        );
    }

    public function setThumbnailAttribute($image)
    {
        $fileStorage = app(FileStorageServiceContract::class);

        if (! empty($this->attributes['thumbnail'])) {
            $fileStorage->remove($this->attributes['thumbnail']);
        }

        $this->attributes['thumbnail'] = $fileStorage->upload(
            $image,
            $this->attributes['directory']
        );
    }

    public function finalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => round(($this->attributes['new_price'] && $this->attributes['new_price'] > 0
                ? $this->attributes['new_price']
                : $this->attributes['price']
            ), 2)
        );
    }

    public function discount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $price = $this->attributes['price'];
                $newPrice = $this->attributes['new_price'];

                if (empty($newPrice) || $newPrice === 0 || $price == $newPrice) {
                    return null;
                } else {
                    return round(($price - $newPrice) / $price, 2) * 100;
                }
            }
        );
    }
}
