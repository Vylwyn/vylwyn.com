<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Decides who may reach the admin panel.
     *
     * Filament calls this on every panel request in any environment other than
     * local. Without the FilamentUser contract it refuses access outright —
     * which is why the panel worked on Herd and returned 403 in production.
     *
     * The allowlist lives in config rather than in code so adding an address
     * doesn't require a deploy. It fails closed: an empty list locks everyone
     * out, including me. That's deliberate — a misconfiguration should deny
     * access, never grant it.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        $allowed = config('portfolio.admin_emails', []);

        if ($allowed === []) {
            return false;
        }

        return in_array(Str::lower($this->email), $allowed, true);
    }
}
