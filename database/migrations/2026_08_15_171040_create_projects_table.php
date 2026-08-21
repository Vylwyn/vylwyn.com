<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('tagline');
            $table->text('summary');

            /** Long-form case study, authored in markdown. Null until written. */
            $table->longText('body')->nullable();

            /** Cast to App\Enums\ProjectStatus on the model. */
            $table->string('status')->default('in_progress');

            /** Null for personal projects. */
            $table->string('client')->nullable();

            /** Nullable so we never invent a date we don't know. */
            $table->year('year')->nullable();

            $table->string('live_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->string('app_store_url')->nullable();
            $table->string('play_store_url')->nullable();

            $table->string('cover_image')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            /** Null means draft. Set means published at that moment. */
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            /** The public listing query filters on published_at and orders by sort_order. */
            $table->index(['published_at', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
