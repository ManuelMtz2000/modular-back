<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $publicacionesColeccion = Publicacion::all();
        $objeto = [];
        $publicaciones = [];
        foreach($publicacionesColeccion as $p){
            $objeto = [
                "id" => $p->id,
                "tipoPublicacion" => $p->tipo_publicacion,
                "autorPublicacion" => self::getNombre($p->autorPublicacion),
                "fotoUsuario" => self::getPerfil($p->autorPublicacion),
                "mostrarContacto" => self::getDatos($p->autorPublicacion, $p->mostrar_contacto),
                "fotoObjeto" => self::getImage($p->foto_objeto),
                "descObjetoC" => $p->desc_objetoC,
                "descDetallada" => $p->desc_detallada,
                "lugar" => $p->lugar
            ];
            $publicaciones[] = $objeto;
        }
        return response()->json($publicaciones);
    }

    public function getImage($imagen){
        return 'http://localhost:8000/img/'.$imagen;
    }

    public function getNombre($id){
        $user = User::where('id', $id)->first();
        return $user->nombre;
    }

    public function getPerfil($id){
        $user = User::where('id', $id)->first();
        return 'http://localhost:8000/img/fotos_p/'.$user->foto_perfil;
    }

    public function getDatos($id, $respuesta){
        $user = User::where('id', $id)->first();
        if($respuesta == 'Si'){
            return $user->datosContacto;
        }
        return null;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $publicacion = new Publicacion();
        $publicacion->tipo_publicacion = $request->input('tipo_publicacion');
        $publicacion->mostrar_contacto = $request->input('mostrar_contacto');
        $publicacion->foto_objeto = null;
        $publicacion->desc_objetoC = $request->input('desc_objetoC');
        $publicacion->desc_detallada = $request->input('desc_detallada');
        $publicacion->autorPublicacion = $request->input('id');
        $publicacion->lugar = $request->input('lugar');

        if($request->hasFile('imagen')){
            $file = $request->file('imagen');
            $filename = $file->getClientOriginalName();
            $picture = date('d-m-y h.i.s A').'-'.$filename;
            $publicacion->foto_objeto = $picture;
            $file->move(public_path('img'), $picture);
        }

        $publicacion->statusPublicacion = 1;
        $publicacion->save();
        return '{"msg": "created"}';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publicacion  $publicacion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $publicacionColeccion = Publicacion::where('id', $id)->first();
        $objeto = [];
            $objeto = [
                "id" => $publicacionColeccion->id,
                "tipoPublicacion" => $publicacionColeccion->tipo_publicacion,
                "autorPublicacion" => self::getNombre($publicacionColeccion->autorPublicacion),
                "mostrarContacto" => self::getDatos($publicacionColeccion->autorPublicacion, $publicacionColeccion->mostrar_contacto),
                "fotoObjeto" => self::getImage($publicacionColeccion->foto_objeto),
                "descObjetoC" => $publicacionColeccion->desc_objetoC,
                "descDetallada" => $publicacionColeccion->desc_detallada,
                "lugar" => $publicacionColeccion->lugar,
                "statusPublicacion" => $publicacionColeccion->statusPublicacion
            ];
        return response()->json($objeto);
    }

    public function reportar(Request $request){
        $publicacion = Publicacion::where('id', $request->input('id'))->first();
        DB::table('reportes')->insert([
            "id_usuario_reporta" => $request->input('idReporta'),
            "id_usuario_reportado" => $publicacion->autorPublicacion,
            "id_publicacion" => $publicacion->id,
            "descripcion" => $request->input('descripcion')
        ]);
    }

    public function edit(Publicacion $publicacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Publicacion  $publicacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Publicacion $publicacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publicacion  $publicacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Publicacion $publicacion)
    {
        //
    }

    public function getByUser($id){
        $publicaciones = Publicacion::where('autorPublicacion', $id)->get();
        $objeto = [];
        $arreglo = [];
        foreach($publicaciones as $p){
            $objeto = [
                "id" => $p->id,
                "tipoPublicacion" => $p->tipo_publicacion,
                "mostrarContacto" => $p->mostrar_contacto,
                "fotoObjeto" => self::getImage($p->foto_objeto),
                "descObjetoC" => $p->desc_objetoC,
                "descDetallada" => $p->desc_detallada,
                "autorPublicacion" => $p->autorPublicacion,
                "lugar" => $p->lugar,
                "statusPublicacion" => $p->statusPublicacion
            ];
            
            $arreglo[] = $objeto;
        }

        return response()->json($arreglo, 200);
    }
}
