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
        Schema::create('usuario_publicacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_publicacion');
            $table->unsignedBigInteger('id_usuarioP');
            $table->unsignedBigInteger('id_usuarioR');
            $table->string('mensaje')->nullable();
            $table->string('folio');
            $table->foreign('id_publicacion')->references('id')->on('publicaciones');
            $table->foreign('id_usuarioP')->references('id')->on('users');
            $table->foreign('id_usuarioR')->references('id')->on('users');
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
        Schema::dropIfExists('usuario_publicacion');
    }
};
