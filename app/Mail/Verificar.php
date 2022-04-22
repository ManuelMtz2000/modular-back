<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Verificar as ModelsVerificar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Verificar extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ModelsVerificar $verificar)
    {
        $this->user = $verificar;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = User::where('id', $this->user->id_usuario)->first();
        $verificar = $this->user;
        return $this->from('extravioscucei01@gmail.com', 'Extravios CUCEI')
        ->view('verificar')->with(compact('user', 'verificar'));
    }
}
