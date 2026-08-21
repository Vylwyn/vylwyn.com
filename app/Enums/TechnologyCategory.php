<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Grouping used by the skills section on the public site.
 */
enum TechnologyCategory: string
{
    case Backend = 'backend';
    case Frontend = 'frontend';
    case Mobile = 'mobile';
    case Tooling = 'tooling';

    public function label(): string
    {
        return match ($this) {
            self::Backend => 'Backend',
            self::Frontend => 'Frontend',
            self::Mobile => 'Mobile',
            self::Tooling => 'Tooling',
        };
    }

    /**
     * Display order for the skills grid, lowest first.
     */
    public function sortOrder(): int
    {
        return match ($this) {
            self::Backend => 1,
            self::Frontend => 2,
            self::Mobile => 3,
            self::Tooling => 4,
        };
    }
}
