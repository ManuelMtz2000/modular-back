<?php

namespace App\Mail;

use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UsuarioPublicacion;

class EnvioCorreo extends Mailable
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
        $user = User::where('id', $this->p->id_usuarioR)->first();
        $imagen = "http://192.168.193.13:8000/img/logo_transparente.png";
        $mensaje = $this->p->mensaje;
        if($mensaje == ''){
            $mensaje = null;
        }
        $publicacion = Publicacion::where('id', $this->p->id_publicacion)->first();
        return $this->from('extravioscucei01@gmail.com', 'Extravios CUCEI')
        ->view('correo')->with(compact('publicacion', 'user', 'mensaje'));
    }
}
