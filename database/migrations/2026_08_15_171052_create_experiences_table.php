<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table): void {
            $table->id();

            $table->string('role');
            $table->string('organisation');
            $table->string('location')->nullable();

            $table->date('started_on');

            /**
             * Null means this is the current role. Deliberately no is_current
             * boolean — two sources of truth for one fact will eventually disagree.
             */
            $table->date('ended_on')->nullable();

            $table->text('summary')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('started_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
