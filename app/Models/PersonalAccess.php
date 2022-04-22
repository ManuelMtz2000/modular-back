<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccess extends Model
{
    use HasFactory;
    protected $table = "personal_access_tokens";
    protected $fillable = [
        "id",
        "tokenable_type",
        "tokenable_id",
        "name",
        "token",
        "abilities",
    ];
    public $timestamps = false;
}
