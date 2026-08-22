<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * The lifecycle state of a portfolio project.
 *
 * Backed by strings rather than integers so the database stays readable —
 * `status = 'live'` is self-explanatory in a query, `status = 1` is not.
 *
 * Implements Filament's HasLabel and HasColor so every select, badge and
 * filter in the admin renders correctly without per-component closures.
 */
enum ProjectStatus: string implements HasColor, HasLabel
{
    case Live = 'live';
    case InProgress = 'in_progress';
    case Archived = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::Live => 'Live',
            self::InProgress => 'In progress',
            self::Archived => 'Archived',
        };
    }

    /**
     * Filament's semantic colour name, used by badges in the admin panel.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::Live => 'success',
            self::InProgress => 'warning',
            self::Archived => 'gray',
        };
    }

    /**
     * Tailwind colour family for the public site's status badge.
     *
     * Deliberately separate from getColor(): the admin uses Filament's
     * semantic palette, the public site uses raw Tailwind families.
     */
    public function tailwindColor(): string
    {
        return match ($this) {
            self::Live => 'emerald',
            self::InProgress => 'amber',
            self::Archived => 'slate',
        };
    }
}
