<x-mail::message>
# New enquiry from your portfolio

**From:** {{ $contactMessage->name }} &lt;{{ $contactMessage->email }}&gt;

@if ($contactMessage->subject)
**Subject:** {{ $contactMessage->subject }}
@endif

**Received:** {{ $contactMessage->created_at->format('j M Y, H:i') }}

---

{{ $contactMessage->message }}

---

<x-mail::button :url="config('app.url') . '/vrdstudio'">
View in admin
</x-mail::button>

Hit reply to respond directly to {{ $contactMessage->name }} — the reply-to address is already set.

<small>Sent from {{ config('app.url') }} · IP {{ $contactMessage->ip_address ?? 'unknown' }}</small>
</x-mail::message>
