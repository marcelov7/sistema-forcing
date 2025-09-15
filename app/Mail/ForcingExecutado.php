<?php

namespace App\Mail;

use App\Models\Forcing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForcingExecutado extends Mailable
{
    use Queueable, SerializesModels;

    public $forcing;

    /**
     * Create a new message instance.
     */
    public function __construct(Forcing $forcing)
    {
        $this->forcing = $forcing;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔧 Forcing Executado - TAG: ' . $this->forcing->tag,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.forcing-executado',
            with: [
                'forcing' => $this->forcing,
                'executante' => $this->forcing->executante,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
