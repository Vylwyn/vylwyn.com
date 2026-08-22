<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends Controller
{
    public function __invoke(Project $project): View
    {
        /**
         * Route model binding found the row, but that only means the slug
         * exists — not that it should be publicly visible. Drafts and
         * scheduled projects must 404 rather than leak.
         */
        if (! $project->isPublished()) {
            throw new NotFoundHttpException;
        }

        $project->load('technologies');

        return view('projects.show', [
            'project' => $project,
            'related' => Project::query()
                ->published()
                ->whereKeyNot($project)
                ->ordered()
                ->with('technologies')
                ->limit(2)
                ->get(),
        ]);
    }
}
