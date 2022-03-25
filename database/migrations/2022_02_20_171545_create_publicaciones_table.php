<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_publicacion');
            $table->unsignedBigInteger('autorPublicacion');
            $table->string('mostrar_contacto');
            $table->string('foto_objeto');
            $table->string('desc_objetoC');
            $table->string('desc_detallada');
            $table->string('lugar');
            $table->unsignedBigInteger('statusPublicacion');
            $table->foreign('autorPublicacion')->references('id')->on('users');
            $table->foreign('statusPublicacion')->references('id')->on('status_publicacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publicaciones');
    }
};
