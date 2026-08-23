<?php

declare(strict_types=1);

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function (): void {
    Mail::fake();
    RateLimiter::clear('contact-form:127.0.0.1');
    config()->set('portfolio.contact.email', 'vylwyn@example.com');
});

/**
 * The component sets renderedAt on mount, and submissions under three seconds
 * old are treated as bots. Tests submit instantly, so they must backdate it.
 */
function contactForm()
{
    return Livewire::test('contact-form')->set('renderedAt', time() - 10);
}

it('saves a valid message', function (): void {
    contactForm()
        ->set('name', 'Jane Recruiter')
        ->set('email', 'jane@example.com')
        ->set('subject', 'Role at our company')
        ->set('message', 'We have a full-stack opening that might suit you well.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(ContactMessage::count())->toBe(1);

    $saved = ContactMessage::first();

    expect($saved->name)->toBe('Jane Recruiter')
        ->and($saved->email)->toBe('jane@example.com')
        ->and($saved->notified)->toBeTrue()
        ->and($saved->read_at)->toBeNull();
});

it('emails the configured address with a reply-to of the sender', function (): void {
    contactForm()
        ->set('name', 'Jane Recruiter')
        ->set('email', 'jane@example.com')
        ->set('message', 'We have a full-stack opening that might suit you well.')
        ->call('submit');

    Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail): bool {
        return $mail->hasTo('vylwyn@example.com')
            && $mail->hasReplyTo('jane@example.com');
    });
});

describe('validation', function (): void {
    it('requires name, email and message', function (): void {
        contactForm()
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'message']);

        expect(ContactMessage::count())->toBe(0);
    });

    it('rejects a malformed email', function (): void {
        contactForm()
            ->set('name', 'Jane')
            ->set('email', 'not-an-email')
            ->set('message', 'A message long enough to pass the minimum length.')
            ->call('submit')
            ->assertHasErrors(['email']);
    });

    it('rejects a message that is too short', function (): void {
        contactForm()
            ->set('name', 'Jane')
            ->set('email', 'jane@example.com')
            ->set('message', 'Hi')
            ->call('submit')
            ->assertHasErrors(['message']);
    });

    it('accepts an empty subject', function (): void {
        contactForm()
            ->set('name', 'Jane')
            ->set('email', 'jane@example.com')
            ->set('message', 'A message long enough to pass the minimum length.')
            ->call('submit')
            ->assertHasNoErrors();

        expect(ContactMessage::first()->subject)->toBeNull();
    });
});

describe('spam protection', function (): void {
    it('silently discards submissions that fill the honeypot', function (): void {
        contactForm()
            ->set('name', 'Spam Bot')
            ->set('email', 'bot@example.com')
            ->set('message', 'Buy cheap watches from our excellent online store.')
            ->set('website', 'http://spam.example.com')
            ->call('submit')
            // Success is shown deliberately — telling a bot it failed helps it.
            ->assertSet('sent', true);

        expect(ContactMessage::count())->toBe(0);
        Mail::assertNothingSent();
    });

    it('silently discards submissions made too quickly', function (): void {
        Livewire::test('contact-form')
            ->set('renderedAt', time())
            ->set('name', 'Fast Bot')
            ->set('email', 'bot@example.com')
            ->set('message', 'A message long enough to pass the minimum length.')
            ->call('submit')
            ->assertSet('sent', true);

        expect(ContactMessage::count())->toBe(0);
    });
});

it('rate limits after three submissions', function (): void {
    foreach (range(1, 3) as $i) {
        contactForm()
            ->set('name', "Sender {$i}")
            ->set('email', "sender{$i}@example.com")
            ->set('message', 'A message long enough to pass the minimum length.')
            ->call('submit')
            ->assertHasNoErrors();
    }

    contactForm()
        ->set('name', 'Sender 4')
        ->set('email', 'sender4@example.com')
        ->set('message', 'A message long enough to pass the minimum length.')
        ->call('submit')
        ->assertHasErrors('message');

    expect(ContactMessage::count())->toBe(3);
});

it('still saves the message when mail fails', function (): void {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP unavailable'));

    contactForm()
        ->set('name', 'Jane Recruiter')
        ->set('email', 'jane@example.com')
        ->set('message', 'A message long enough to pass the minimum length.')
        ->call('submit')
        ->assertSet('sent', true);

    // The enquiry survives even though the notification did not.
    expect(ContactMessage::count())->toBe(1)
        ->and(ContactMessage::first()->notified)->toBeFalse();
});
