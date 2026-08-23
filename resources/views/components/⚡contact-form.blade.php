<?php

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Validate('required|string|min:2|max:100')]
    public string $name = '';

    /**
     * `email:rfc` only, deliberately not `dns`. The DNS variant performs a live
     * MX lookup on every submission, which makes validation network-dependent —
     * slow tests, flaky CI, and a form that breaks when DNS hiccups.
     */
    #[Validate('required|email:rfc|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:150')]
    public string $subject = '';

    #[Validate('required|string|min:20|max:3000')]
    public string $message = '';

    /**
     * Honeypot. Hidden from humans by CSS, irresistible to naive bots.
     * A real visitor can never fill this in, so any value means spam.
     */
    public string $website = '';

    /**
     * Timestamp of when the form was rendered. Submissions faster than a few
     * seconds are almost certainly automated — humans cannot type 20 characters
     * of message that quickly.
     */
    public int $renderedAt = 0;

    public bool $sent = false;

    public function mount(): void
    {
        $this->renderedAt = time();
    }

    public function submit(): void
    {
        /**
         * Rate limit before validation. Otherwise an attacker gets free
         * validation feedback on every attempt without ever being throttled.
         */
        $key = 'contact-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, maxAttempts: 3)) {
            $this->addError('message', sprintf(
                'Too many attempts. Please try again in %d seconds.',
                RateLimiter::availableIn($key),
            ));

            return;
        }

        $this->validate();

        /**
         * Both spam checks fail silently — we show the success state without
         * saving anything. Telling a bot why it was rejected just helps the
         * next version of the bot.
         */
        if (filled($this->website) || (time() - $this->renderedAt) < 3) {
            Log::info('Contact form spam rejected', ['ip' => request()->ip()]);

            $this->reset(['name', 'email', 'subject', 'message', 'website']);
            $this->sent = true;

            return;
        }

        RateLimiter::hit($key, decaySeconds: 900);

        $contactMessage = ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject ?: null,
            'message' => $this->message,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);

        /**
         * The message is already saved. If mail fails — and shared-host SMTP
         * does fail — we log it and still show success, because from the
         * visitor's perspective the enquiry did arrive. The `notified` column
         * records the difference so it's visible in the admin panel.
         */
        try {
            if (filled(config('portfolio.contact.email'))) {
                Mail::to(config('portfolio.contact.email'))
                    ->send(new ContactMessageReceived($contactMessage));

                $contactMessage->update(['notified' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Contact notification failed', [
                'contact_message_id' => $contactMessage->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->reset(['name', 'email', 'subject', 'message']);
        $this->sent = true;
    }
};
?>

<div class="mx-auto max-w-xl text-left">

    @if ($sent)
        <div role="status"
             class="rounded-2xl border border-ok/30 bg-ok/10 px-6 py-8 text-center">
            <p class="mb-2 text-lg font-bold text-ok">Message sent</p>
            <p class="text-sm text-dim">Thanks — I'll get back to you shortly.</p>

            <button type="button"
                    wire:click="$set('sent', false)"
                    class="mt-5 text-sm font-semibold text-lavender transition hover:text-violet">
                Send another
            </button>
        </div>
    @else
        <form wire:submit="submit" class="space-y-4">

            {{-- Honeypot. Hidden from sighted users and from screen readers,
                 and removed from the tab order, so no human can reach it. --}}
            <div aria-hidden="true" class="absolute -left-[9999px] h-0 w-0 overflow-hidden">
                <label for="website">Website</label>
                <input type="text" id="website" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-dim">
                        Name <span class="text-rose" aria-hidden="true">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           wire:model.blur="name"
                           required
                           autocomplete="name"
                           @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                           class="w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink placeholder:text-faint focus:border-violet focus:outline-none">
                    @error('name')
                        <p id="name-error" class="mt-1.5 text-xs text-rose">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-dim">
                        Email <span class="text-rose" aria-hidden="true">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           wire:model.blur="email"
                           required
                           autocomplete="email"
                           @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                           class="w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink placeholder:text-faint focus:border-violet focus:outline-none">
                    @error('email')
                        <p id="email-error" class="mt-1.5 text-xs text-rose">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="subject" class="mb-1.5 block text-sm font-medium text-dim">Subject</label>
                <input type="text"
                       id="subject"
                       wire:model.blur="subject"
                       class="w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink placeholder:text-faint focus:border-violet focus:outline-none">
                @error('subject')
                    <p class="mt-1.5 text-xs text-rose">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="mb-1.5 block text-sm font-medium text-dim">
                    Message <span class="text-rose" aria-hidden="true">*</span>
                </label>
                <textarea id="message"
                          wire:model.blur="message"
                          rows="5"
                          required
                          @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
                          class="w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink placeholder:text-faint focus:border-violet focus:outline-none"></textarea>
                @error('message')
                    <p id="message-error" class="mt-1.5 text-xs text-rose">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet to-azure px-6.5 py-3.5 text-sm font-semibold text-white shadow-[0_8px_26px_-8px_rgba(139,92,246,0.6)] transition duration-300 hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto">
                <span wire:loading.remove wire:target="submit">Send message <span aria-hidden="true">→</span></span>
                <span wire:loading wire:target="submit">Sending…</span>
            </button>
        </form>
    @endif
</div>
