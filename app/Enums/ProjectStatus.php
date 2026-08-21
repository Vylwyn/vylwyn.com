<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The lifecycle state of a portfolio project.
 *
 * Backed by strings rather than integers so the database stays readable —
 * `status = 'live'` is self-explanatory in a query, `status = 1` is not.
 */
enum ProjectStatus: string
{
    case Live = 'live';
    case InProgress = 'in_progress';
    case Archived = 'archived';

    /**
     * Human-readable label for the admin panel and public badges.
     */
    public function label(): string
    {
        return match ($this) {
            self::Live => 'Live',
            self::InProgress => 'In progress',
            self::Archived => 'Archived',
        };
    }

    /**
     * Tailwind colour token used by the status badge component.
     */
    public function color(): string
    {
        return match ($this) {
            self::Live => 'emerald',
            self::InProgress => 'amber',
            self::Archived => 'slate',
        };
    }
}
