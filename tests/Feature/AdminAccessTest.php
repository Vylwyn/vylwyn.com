<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Facades\Filament;

/**
 * Guards on who can reach the admin panel.
 *
 * These matter more than most tests here: the failure mode isn't a broken
 * page, it's a stranger editing your site.
 */
function panel()
{
    return Filament::getPanel('vrdstudio');
}

it('allows an allowlisted address', function (): void {
    config()->set('portfolio.admin_emails', ['vylwyndsouza@gmail.com']);

    $user = User::factory()->create(['email' => 'vylwyndsouza@gmail.com']);

    expect($user->canAccessPanel(panel()))->toBeTrue();
});

it('denies an address that is not allowlisted', function (): void {
    config()->set('portfolio.admin_emails', ['vylwyndsouza@gmail.com']);

    $user = User::factory()->create(['email' => 'someone-else@example.com']);

    expect($user->canAccessPanel(panel()))->toBeFalse();
});

it('denies everyone when the allowlist is empty', function (): void {
    // Fail closed: a missing ADMIN_EMAILS must lock the panel, not open it.
    config()->set('portfolio.admin_emails', []);

    $user = User::factory()->create(['email' => 'vylwyndsouza@gmail.com']);

    expect($user->canAccessPanel(panel()))->toBeFalse();
});

it('matches the allowlist case-insensitively', function (): void {
    config()->set('portfolio.admin_emails', ['vylwyndsouza@gmail.com']);

    $user = User::factory()->create(['email' => 'Vylwyn.Dsouza@Gmail.com']);

    expect($user->canAccessPanel(panel()))->toBeFalse();

    $exact = User::factory()->create(['email' => 'VYLWYNDSOUZA@GMAIL.COM']);

    expect($exact->canAccessPanel(panel()))->toBeTrue();
});

it('redirects anonymous visitors to the login page', function (): void {
    $this->get('/vrdstudio')->assertRedirect('/vrdstudio/login');
});
