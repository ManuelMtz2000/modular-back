<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioPublicacion extends Model
{
    use HasFactory;
    protected $table = "usuario_publicacion";
    protected $fillable = [
        "id",
        "id_publicacion",
        "id_usuarioP",
        "id_usuarioR",
        "mensaje",
        "folio",
        "created_at",
        "updated_at"
    ];
}
