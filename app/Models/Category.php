<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
    use HasFactory, Sortable;

    protected $table = 'categories';

    public $sortable = [
        'id',
        'name',
        'parent_id',
        'updated_at',
    ];

    public $sortableAs = ['products_count'];

    protected $fillable = [
        'slug',
        'name',
        'parent_id',
    ];

    protected $hidden = [];

    protected $casts = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function childs(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
