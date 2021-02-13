<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificarEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Nome do novo usuário.
     *
     * @var string
     */
    public $nome;

    /**
     * URL de verificacao.
     *
     * @var string
     */
    public $url;

    /**
     * Create a new message instance.
     *
     * @param  string $nome Nome do novo usuário
     * @return void
     */
    public function __construct(string $nome, string $url)
    {
        $this->nome = $nome;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.auth.verificar-email');
    }
}
