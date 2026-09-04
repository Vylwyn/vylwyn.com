<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Singleton holding the editable page copy.
 *
 * Only ever one row. current() is the only supported way to read it.
 */
class SiteContent extends Model
{
    private const CACHE_KEY = 'site-content';

    protected $guarded = [];

    /**
     * The single row, cached.
     *
     * Every page view needs this, so an uncached read would add a query to
     * every request for content that changes a few times a year.
     *
     * Note what is cached: a plain ARRAY of attributes, never the model.
     * Caching an Eloquent object serialises its class name, and if that class
     * cannot be resolved on a later request — mid-deploy, say — PHP returns
     * __PHP_Incomplete_Class instead. Combined with rememberForever that is a
     * permanent 500 which only a manual cache:clear can fix. An array has no
     * class identity to lose, so it survives any deploy.
     */
    public static function current(): self
    {
        $attributes = Cache::rememberForever(
            self::CACHE_KEY,
            static fn (): array => static::query()->first()?->attributesToArray() ?? [],
        );

        return (new self)->forceFill($attributes);
    }

    protected static function booted(): void
    {
        /**
         * Flushing on both saved and deleted means the cache can never
         * outlive the data. Forgetting this is the classic caching bug:
         * the admin saves, the site doesn't change, and you lose an hour.
         */
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Read a field, falling back to config when the row or value is missing.
     *
     * Means the site still renders sensibly before the table is seeded —
     * a fresh install shows the config defaults rather than blank headings.
     */
    public function valueOr(string $field, ?string $fallback = null): string
    {
        return filled($this->{$field}) ? (string) $this->{$field} : (string) $fallback;
    }
}
