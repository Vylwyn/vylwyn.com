<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_contents', function (Blueprint $table): void {
            /** Storage path on the public disk. Null falls back to the placeholder. */
            $table->string('photo')->nullable()->after('about_body');
        });
    }

    public function down(): void
    {
        Schema::table('site_contents', function (Blueprint $table): void {
            $table->dropColumn('photo');
        });
    }
};
