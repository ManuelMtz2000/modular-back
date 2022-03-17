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
        "mostrar_contacto",
        "foto_objeto",
        "desc_objetoC",
        "desc_detallada",
        "lugar",
    ];
    use HasFactory;
}
