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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('contrasenia');
            $table->string('foto_identificacion');
            $table->string('foto_perfil')->nullable();
            $table->unsignedBigInteger('tipo_usuario_id');
            $table->string('curp', 18);
            $table->text('datosContacto')->nullable();
            $table->rememberToken();
            $table->foreign('tipo_usuario_id')->references('id')->on('tipo_usuario');
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
        Schema::dropIfExists('users');
    }
};
