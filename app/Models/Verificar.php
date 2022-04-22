<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verificar extends Model
{
    use HasFactory;
    protected $table = "verificar";
    protected $fillable = [
        "id",
        "id_usuario",
        "codigo"
    ];
}
