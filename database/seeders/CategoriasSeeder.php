<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categorias')->insert([
            "nombre_categoria" => 'Electrónicos',
        ]);

        DB::table('categorias')->insert([
            "nombre_categoria" => 'Documentos',
        ]);

        DB::table('categorias')->insert([
            "nombre_categoria" => 'Otros',
        ]);
    }
}
