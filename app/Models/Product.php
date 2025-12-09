<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

final class Product extends Model
{
    /** @use HasFactory<ProductFactory> **/
    use HasFactory;

    use HasSlug;
    use HasUlids;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'ProductID',
        'brand_id',
        'slug',
        'Product',
        'ProductName',
        'newSystem',
        'Visible',
        'flyer',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('ProductName')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<Brand, $this> */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('products')
            ->logFillable()
            ->logOnlyDirty();
    }

    /** @return HasMany<Expenses, $this> */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expenses::class, 'product_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'newSystem' => 'boolean',
            'Visible' => 'boolean',
            'flyer' => 'boolean',
        ];
    }
}
