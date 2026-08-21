<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(ExperienceFactory::class)]
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_on' => 'date',
            'ended_on' => 'date',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Most recent role first.
     *
     * @param  Builder<Experience>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('started_on');
    }

    /**
     * A null end date means this is the role held right now.
     */
    public function isCurrent(): bool
    {
        return $this->ended_on === null;
    }

    /**
     * "Jul 2017 — Present" or "Jun 2016 — Jul 2017".
     */
    public function period(): string
    {
        $start = $this->started_on->format('M Y');
        $end = $this->isCurrent() ? 'Present' : $this->ended_on->format('M Y');

        return "{$start} — {$end}";
    }

    /**
     * "9 yrs 2 mos", matching how LinkedIn presents tenure.
     */
    public function duration(): string
    {
        $end = $this->ended_on ?? now();

        $years = (int) $this->started_on->diffInYears($end);
        $months = (int) $this->started_on->copy()->addYears($years)->diffInMonths($end);

        $parts = [];

        if ($years > 0) {
            $parts[] = $years.' '.str('yr')->plural($years);
        }

        if ($months > 0) {
            $parts[] = $months.' '.str('mo')->plural($months);
        }

        return $parts === [] ? 'Less than a month' : implode(' ', $parts);
    }

    public function endedOn(): ?CarbonInterface
    {
        return $this->ended_on;
    }
}
