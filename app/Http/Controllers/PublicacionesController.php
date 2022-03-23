<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;

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
                "mostrar_contacto" => $p->mostrar_contacto,
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
        $publicacion->lugar = $request->input('lugar');

        if($request->hasFile('imagen')){
            $file = $request->file('imagen');
            $filename = $file->getClientOriginalName();
            $picture = date('d-m-y h.i.s A').'-'.$filename;
            $publicacion->foto_objeto = $picture;
            $file->move(public_path('img'), $picture);
        }

        $publicacion->save();
        return '{"msg": "created"}';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publicacion  $publicacion
     * @return \Illuminate\Http\Response
     */
    public function show(Publicacion $publicacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Publicacion  $publicacion
     * @return \Illuminate\Http\Response
     */
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
}
