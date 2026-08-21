<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TechnologyCategory;
use Database\Factories\TechnologyFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UseFactory(TechnologyFactory::class)]
class Technology extends Model
{
    /** @use HasFactory<TechnologyFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => TechnologyCategory::class,
            'show_in_skills' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * Technologies that should appear in the public skills grid.
     *
     * @param  Builder<Technology>  $query
     */
    public function scopeForSkills(Builder $query): void
    {
        $query->where('show_in_skills', true)->orderBy('sort_order');
    }

    /**
     * @param  Builder<Technology>  $query
     */
    public function scopeInCategory(Builder $query, TechnologyCategory $category): void
    {
        $query->where('category', $category);
    }
}
