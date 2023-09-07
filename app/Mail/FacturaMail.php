<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $view, $subject, $content, $nameFile, $date;
    public $nombre, $identificacion;
    public $factura_numero, $factura_autorizacion, $factura_total;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($view, $date, $subject, $factura_numero, $factura_autorizacion, $factura_total, $identificacion, $nombre, $content = null, $nameFile = null)
    {
        $this->view = $view;
        $this->date = $date;
        $this->subject = $subject;
        $this->nombre = $nombre;
        $this->identificacion = $identificacion;
        $this->factura_numero = $factura_numero;
        $this->factura_autorizacion = $factura_autorizacion;
        $this->factura_total = $factura_total;
        $this->content = $content;
        $this->nameFile = $nameFile;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: $this->view,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [
            Attachment::fromPath(storage_path('app/comprobantes/autorizados/') . $this->content[0])->as($this->factura_autorizacion . '.xml')->withMime('application/xml'),
            Attachment::fromPath(storage_path('app/comprobantes/RIDE/') . $this->content[1])->as($this->factura_autorizacion . '.pdf')->withMime('application/pdf'),
            // Attachment::fromData(fn () => $this->content[1], $this->factura_autorizacion . '.pdf')->withMime('application/pdf'),,
        ];
    }
}
