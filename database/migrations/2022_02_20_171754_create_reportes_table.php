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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario_reporta');
            $table->unsignedBigInteger('id_usuario_reportado');
            $table->unsignedBigInteger('id_publicacion');
            $table->string('descripcion');
            $table->foreign('id_usuario_reporta')->references('id')->on('users');
            $table->foreign('id_usuario_reportado')->references('id')->on('users');
            $table->foreign('id_publicacion')->references('id')->on('publicaciones');
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
        Schema::dropIfExists('reportes');
    }
};
