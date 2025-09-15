<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SolicitacoesRetiradaCondensada extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $forcings;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $forcings)
    {
        $this->forcings = $forcings;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $count = $this->forcings->count();
        
        return new Envelope(
            subject: "🔄 {$count} Solicitaç" . ($count > 1 ? 'ões' : 'ão') . " de Retirada de Forcing Pendente" . ($count > 1 ? 's' : ''),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitacoes-retirada-condensada',
            with: [
                'forcings' => $this->forcings,
                'totalForcings' => $this->forcings->count(),
            ]
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
