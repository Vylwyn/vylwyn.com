<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TechnologyCategory;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SiteContent;
use App\Models\Technology;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'content' => SiteContent::current(),
            'projects' => $this->featuredProjects(),
            'experiences' => Experience::ordered()->get(),
            'skills' => $this->skillsByCategory(),
        ]);
    }

    /**
     * @return Collection<int, Project>
     */
    private function featuredProjects(): Collection
    {
        return Project::query()
            ->published()
            ->featured()
            ->ordered()
            /**
             * Eager load the pivot. Without this, rendering N project cards
             * fires N extra queries for their technologies — the N+1 problem.
             */
            ->with('technologies')
            ->get();
    }

    /**
     * Skills grouped into the four categories the design expects.
     *
     * Grouping in PHP rather than running four queries: one trip to the
     * database, then a Collection operation in memory.
     *
     * @return Collection<string, Collection<int, Technology>>
     */
    private function skillsByCategory(): Collection
    {
        return Technology::query()
            ->forSkills()
            ->get()
            ->groupBy(fn (Technology $technology): string => $technology->category->value)
            ->sortBy(fn (Collection $group, string $category): int => TechnologyCategory::from($category)->sortOrder());
    }
}
