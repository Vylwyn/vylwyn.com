<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatus;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UseFactory(ProjectFactory::class)]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Use the slug in URLs instead of the numeric id.
     *
     * Gives you /work/coolpex-water-filters rather than /work/1, with no
     * extra query logic in the controller.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsToMany<Technology, $this>
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->orderBy('sort_order');
    }

    /**
     * Only projects that have actually been published.
     *
     * @param  Builder<Project>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<Project>  $query
     */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /**
     * @param  Builder<Project>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('year');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    public function hasCaseStudy(): bool
    {
        return filled($this->body);
    }

    /**
     * Only the links that are actually populated, ready to loop over in Blade.
     *
     * Avoids four @if blocks in the view, and means adding a link type later
     * is a change in one place rather than in every template.
     *
     * @return array<int, array{label: string, url: string, icon: string}>
     */
    public function links(): array
    {
        $candidates = [
            ['label' => 'Live site', 'url' => $this->live_url, 'icon' => 'external'],
            ['label' => 'Source', 'url' => $this->repo_url, 'icon' => 'github'],
            ['label' => 'App Store', 'url' => $this->app_store_url, 'icon' => 'apple'],
            ['label' => 'Google Play', 'url' => $this->play_store_url, 'icon' => 'android'],
        ];

        return array_values(
            array_filter($candidates, static fn (array $link): bool => filled($link['url']))
        );
    }
}
