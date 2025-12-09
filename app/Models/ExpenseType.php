<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ExpenseTypeFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

final class ExpenseType extends Model
{
    /** @use HasFactory<ExpenseTypeFactory> **/
    use HasFactory;

    use HasUlids;
    use LogsActivity;
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'ExpenseID',
        'Name',
        'Visible',
        'slug',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('Name')
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('expense_types')
            ->logFillable()
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'Visible' => 'boolean',
        ];
    }
}
