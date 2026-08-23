<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table): void {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');

            /**
             * Captured for spam triage and nothing else. IPv6 needs 45 chars.
             * Both nullable — a missing user agent must never block a genuine
             * enquiry from being saved.
             */
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            /** Null until you mark it read in the admin panel. */
            $table->timestamp('read_at')->nullable();

            /**
             * Whether the notification email actually went out. Shared-host SMTP
             * fails often enough that "saved but not emailed" is a real state
             * worth being able to see.
             */
            $table->boolean('notified')->default(false);

            $table->timestamps();

            $table->index(['read_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
