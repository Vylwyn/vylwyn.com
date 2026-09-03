<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Editable page copy — a singleton table holding exactly one row.
     *
     * Typed columns rather than a generic key/value settings table: the form
     * fields, validation and Blade usage all stay explicit, and a typo in a
     * key can't silently return null. The tradeoff is that adding a field
     * needs a migration, which for page copy is a fair price.
     */
    public function up(): void
    {
        Schema::create('site_contents', function (Blueprint $table): void {
            $table->id();

            // Hero
            $table->string('hero_name');
            $table->string('hero_role');
            $table->string('hero_specialisms')->nullable();
            $table->string('hero_tagline_lead');
            $table->string('hero_tagline_highlight')->nullable();
            $table->text('hero_lede')->nullable();

            // Section introductions
            $table->string('work_heading')->default('Things I’ve shipped');
            $table->text('work_intro')->nullable();
            $table->string('experience_heading')->default('Eleven years, two countries');
            $table->text('experience_intro')->nullable();

            // About
            $table->string('about_heading')->default('Hey, I’m Vylwyn');
            $table->longText('about_body')->nullable();

            // Contact
            $table->string('contact_heading')->default('Let’s build something');
            $table->text('contact_intro')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_contents');
    }
};
