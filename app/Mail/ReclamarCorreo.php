<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UsuarioPublicacion;
use App\Models\Publicacion;
use App\Models\User;

class ReclamarCorreo extends Mailable
{
    use Queueable, SerializesModels;
    private $p;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(UsuarioPublicacion $p)
    {
        $this->p = $p;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = User::where('id', $this->p->id_usuarioP)->first();
        $imagen = "http://192.168.193.13:8000/img/logo_transparente.png";
        $folio = $this->p->folio;
        $publicacion = Publicacion::where('id', $this->p->id_publicacion)->first();
        return $this->from('extravioscucei01@gmail.com', 'Extravios CUCEI')
        ->view('reclama')->with(compact('publicacion', 'user', 'folio'));
    }
}
