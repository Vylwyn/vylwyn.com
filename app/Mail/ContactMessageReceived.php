<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Portfolio enquiry from '.$this->contactMessage->name,

            /**
             * The From address stays yours — sending as the visitor's address
             * would fail SPF and DKIM checks and land the mail in spam.
             * Reply-To is what makes hitting reply work correctly.
             */
            replyTo: [
                new Address($this->contactMessage->email, $this->contactMessage->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact-message',
        );
    }
}
