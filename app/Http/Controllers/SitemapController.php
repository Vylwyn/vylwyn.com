<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;
use XMLWriter;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        /**
         * Only published projects that actually have a case study get a URL.
         * Advertising a page that renders nothing wastes crawl budget and
         * reads as thin content.
         */
        $projects = Project::query()
            ->published()
            ->whereNotNull('body')
            ->ordered()
            ->get();

        /**
         * Built with XMLWriter rather than a Blade view.
         *
         * Blade compiles to PHP, and PHP's lexer opens a code block the moment
         * it sees `<?` — so an XML declaration in a template is parsed as PHP
         * and blows up. XMLWriter also escapes values correctly, which matters
         * for URLs containing ampersands.
         */
        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('    ');

        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        $this->writeUrl($xml, route('home'), changefreq: 'monthly', priority: '1.0');

        foreach ($projects as $project) {
            $this->writeUrl(
                $xml,
                route('projects.show', $project),
                changefreq: 'yearly',
                priority: '0.8',
                lastmod: $project->updated_at?->toAtomString(),
            );
        }

        $xml->endElement();
        $xml->endDocument();

        return response($xml->outputMemory())
            ->header('Content-Type', 'application/xml');
    }

    private function writeUrl(
        XMLWriter $xml,
        string $location,
        string $changefreq,
        string $priority,
        ?string $lastmod = null,
    ): void {
        $xml->startElement('url');
        $xml->writeElement('loc', $location);

        if ($lastmod !== null) {
            $xml->writeElement('lastmod', $lastmod);
        }

        $xml->writeElement('changefreq', $changefreq);
        $xml->writeElement('priority', $priority);
        $xml->endElement();
    }
}
