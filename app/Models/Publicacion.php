<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    protected $table = "publicaciones";
    protected $fillable = [
        "id",
        "tipo_publicacion_id",
        "autorPublicacion",
        "mostrar_contacto",
        "foto_objeto",
        "desc_objetoC",
        "desc_detallada",
        "categoriasPublicacion",
        "lugar",
        "statusPublicacion"
    ];
    use HasFactory;
}
