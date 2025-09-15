<?php

namespace App\Mail;

use App\Models\Forcing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmacaoRetiradaForcing extends Mailable
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
            subject: '🎉 Forcing Finalizado - TAG: ' . $this->forcing->tag . ' - Ciclo Completo',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.confirmacao-retirada-forcing',
            with: [
                'forcing' => $this->forcing,
                'solicitante' => $this->forcing->user,
                'liberador' => $this->forcing->liberador,
                'executante' => $this->forcing->executante,
                'retiradoPor' => $this->forcing->retiradoPor,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
