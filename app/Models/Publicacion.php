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

    public function setDescObjetoCAttribute($value)
    {
        $this->attributes['desc_objetoC'] = strtolower($value);
    }

    public function setDescDetalladaAttribute($value)
    {
        $this->attributes['desc_detallada'] = strtolower($value);
    }

    public function setLugarAttribute($value)
    {
        $this->attributes['lugar'] = strtolower($value);
    }

    public function getDescObjetoCAttribute($value)
    {
        return ucfirst($value);
    }

    public function getDescDetalladaAttribute($value)
    {
        return ucfirst($value);
    }

    public function getLugarAttribute($value)
    {
        return ucfirst($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categorias()
    {
        return $this->hasOne(Categoria::class);
    }

}
