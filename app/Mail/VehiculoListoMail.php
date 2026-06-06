<?php

namespace App\Mail;

use App\Models\OrdenTrabajo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VehiculoListoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public OrdenTrabajo $orden) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Su vehículo está listo — Taller García',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vehiculo-listo',
        );
    }
}
