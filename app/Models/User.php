<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'correo',
        'contrasenia',
        'foto_identificacion',
        'foto_perfil',
        'tipo_usuario_id',
        'curp',
        'datosContacto'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function publicacion()
    {
        return $this->hasMany(Publicacion::class, 'autorPublicacion');
    }

    public function usuarioreclama()
    {
        return $this->hasMany(UsuarioPublicacion::class, 'id_usuarioR');
    }

    public function usuariopublica()
    {
        return $this->hasMany(UsuarioPublicacion::class, 'id_usuarioP');
    }

}
