<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AyudaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ayuda')->insert([
            "preguntas_frecuentes" => '¿Cómo crear una publicación?',
            "isVideo" => 'Si',
            "videos" => '<iframe allowFullScreen width="560" height="315" src="https://www.youtube.com/embed/aSJ9wtQbLtY" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>'
        ]);
        DB::table('ayuda')->insert([
            "preguntas_frecuentes" => '¿Cómo reporto una publicación?',
            "isVideo" => 'Si',
            "videos" => '<iframe className="mb-2 md:w-1/2 md:h-80" allowFullScreen src="https://www.youtube.com/embed/Kkdhtb9DVWQ" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>'
        ]);
        DB::table('ayuda')->insert([
            "preguntas_frecuentes" => 'Si detecto un fallo, ¿Con quién lo reporto?',
            "isVideo" => 'No',
            "videos" => 'Centro de objetos extraviados.'
        ]);
    }
}
