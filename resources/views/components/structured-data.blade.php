@props(['project' => null])

@php
    /**
     * JSON-LD structured data.
     *
     * This is how Google builds a knowledge panel for you when someone searches
     * your name — it disambiguates "Vylwyn D'Souza the developer" from anyone
     * else with the name, and links your site to your LinkedIn and GitHub
     * profiles as verified identities.
     *
     * Two graphs: a Person (always) and a CreativeWork (on case study pages).
     */
    $person = array_filter([
        '@type' => 'Person',
        '@id' => url('/') . '#person',
        'name' => config('portfolio.full_name'),
        'alternateName' => config('portfolio.name'),
        'url' => url('/'),
        'image' => asset('og-image.png'),
        'jobTitle' => config('portfolio.role'),
        'description' => config('portfolio.seo.description'),
        'email' => config('portfolio.contact.email')
            ? 'mailto:' . config('portfolio.contact.email')
            : null,
        'address' => [
            '@type' => 'PostalAddress',
            'addressCountry' => config('portfolio.location'),
        ],
        'worksFor' => [
            '@type' => 'Organization',
            'name' => 'Alghanim International',
        ],
        'alumniOf' => [
            '@type' => 'EducationalOrganization',
            'name' => 'Master of Computer Applications (MCA), Computer Science',
        ],
        'knowsAbout' => [
            'Laravel', 'PHP', 'Flutter', 'Dart', 'MySQL',
            'IT service management', 'Procurement', 'Internal tools',
        ],
        'sameAs' => array_values(array_filter([
            config('portfolio.contact.linkedin'),
            config('portfolio.contact.github'),
        ])),
    ]);

    $graph = [$person];

    if ($project) {
        $graph[] = array_filter([
            '@type' => 'CreativeWork',
            '@id' => route('projects.show', $project) . '#work',
            'name' => $project->title,
            'description' => $project->summary,
            'url' => route('projects.show', $project),
            'author' => ['@id' => url('/') . '#person'],
            'dateCreated' => $project->year ? (string) $project->year : null,
            'keywords' => $project->technologies->pluck('name')->implode(', ') ?: null,
        ]);
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    ];
@endphp

{{-- JSON_UNESCAPED_SLASHES keeps URLs readable; JSON_UNESCAPED_UNICODE preserves
     the typographic apostrophe in D'Souza rather than emitting ’. --}}
<script type="application/ld+json">
    {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) !!}
</script>
