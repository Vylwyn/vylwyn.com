<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Pivot table for the Project <-> Technology many-to-many relationship.
         *
         * Named singular and alphabetical (project_technology) so Eloquent's
         * belongsToMany() resolves it by convention with no arguments.
         */
        Schema::create('project_technology', function (Blueprint $table): void {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();

            /**
             * Composite primary key. Prevents the same technology being attached
             * to a project twice, enforced by the database rather than by hope.
             */
            $table->primary(['project_id', 'technology_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_technology');
    }
};
