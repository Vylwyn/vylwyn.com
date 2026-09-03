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
     * every request for content that changes a few times a year. The cache is
     * cleared in booted() below, so an edit in the admin panel is visible
     * immediately rather than after some expiry.
     */
    public static function current(): self
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            static fn (): self => static::query()->firstOrNew([]),
        );
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
