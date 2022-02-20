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
        Schema::create('categorias_publicacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_publicacion');
            $table->unsignedBigInteger('id_categoria');
            $table->foreign('id_publicacion')->references('id')->on('publicaciones');
            $table->foreign('id_categoria')->references('id')->on('categorias');
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
        Schema::dropIfExists('categorias_publicacion');
    }
};
