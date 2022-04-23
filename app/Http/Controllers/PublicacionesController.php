<?php

namespace App\Http\Controllers;

use App\Mail\EnvioCorreo;
use App\Mail\ReclamarCorreo;
use App\Models\Categoria;
use App\Models\Publicacion;
use App\Models\User;
use App\Models\UsuarioPublicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PublicacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $publicacionesColeccion = Publicacion::where('statusPublicacion', 1)->get();
        $objeto = [];
        $publicaciones = [];
        foreach($publicacionesColeccion as $p){
            $objeto = [
                "id" => $p->id,
                "tipoPublicacion" => $p->tipo_publicacion,
                "categoriasPublicacion" => self::categoria($p->categoriasPublicacion),
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

    public function categoria($categoriasPublicacion){
        $categoria = Categoria::where('id', $categoriasPublicacion)->first();
        return $categoria->nombre_categoria;
    }

    public function getImage($imagen){
        return 'http://192.168.193.13:8000/img/'.$imagen;
    }

    public function getNombre($id){
        $user = User::where('id', $id)->first();
        return $user->nombre;
    }

    public function getPerfil($id){
        $user = User::where('id', $id)->first();
        return 'http://192.168.193.13:8000/img/fotos_p/'.$user->foto_perfil;
    }

    public function getDatos($id, $respuesta){
        $user = User::where('id', $id)->first();
        if($respuesta == 'Si'){
            return $user->datosContacto;
        }
        return null;
    }

    public function prueba(){
        Storage::disk('local')->append('extravios.pl', PHP_EOL.'Contents', null);
        return "termino";
    }

    public function reclamar(Request $request){
        if(!UsuarioPublicacion::where('id_usuarioR', $request->input('id'))->where('id_publicacion', $request->input('publicacion'))->first())
        {
            $p = Publicacion::where('id', $request->input('publicacion'))->first();
            $publicacion = new UsuarioPublicacion();
            $publicacion->id_usuarioP = $p->autorPublicacion;
            $publicacion->id_usuarioR = $request->input('id');
            $publicacion->id_publicacion = $p->id;
            if($request->input('mensaje') == 'undefined'){
                $publicacion->mensaje = null;
            } else {
                $publicacion->mensaje = $request->input('mensaje');
            }
            $publicacion->folio = Str::random(5);
            $publicacion->save();
            self::correo($publicacion);
        } else {
            abort(404);
        }
    }

    public function correo(UsuarioPublicacion $publicacion){
        $user = User::where('id', $publicacion->id_usuarioP)->first();
        $user2 = User::where('id', $publicacion->id_usuarioR)->first();
        Mail::to($user2->correo)->send(new ReclamarCorreo($publicacion));
        Mail::to($user->correo)->send(new EnvioCorreo($publicacion));
    }

    public function cerrarPublicacion(Request $request, $id){
        $publicacion = UsuarioPublicacion::where('id_publicacion', $id)->where('folio', $request->input('folio'))->first();
        $publicacion2 = Publicacion::where('id', $id)->first();
        if($publicacion){
            if($publicacion2->statusPublicacion == 1){
                DB::table('publicaciones')->where('id', $id)->update([
                    "statusPublicacion" => 2
                ]);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
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
        $publicacion->categoriasPublicacion = $request->input('categoriasPublicacion');
        if($request->hasFile('imagen')){
            $file = $request->file('imagen');
            $filename = $file->getClientOriginalName();
            $picture = date('d-m-y h.i.s A').'-'.$filename;
            $publicacion->foto_objeto = $picture;
            $file->move(public_path('img'), $picture);
        }
        $publicacion->statusPublicacion = 1;
        $publicacion->save();
        $etiquetas = json_decode($request->input('etiquetas'));
        foreach($etiquetas as $e){
            Storage::disk('local')->append('extravios.pl', PHP_EOL.'etiquetas('.$publicacion->id.', '.$e.').', null);
        }
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

    public function search(Request $request){
        $output = `swipl -s C:\Users\Manue\OneDrive\Escritorio\Modular\\extravios-cucei-back\storage\app\\extravios.pl -g "buscar({$request->input('busqueda')})." -t halt.`;
        $arreglo = str_split($output);
        $coleccion = Publicacion::where('desc_objetoC', 'like', '%'.$request->input('busqueda').'%')
            ->orWhere('desc_detallada', 'like', '%'.$request->input('busqueda').'%')
            ->orWhere('lugar', 'like', '%'.$request->input('busqueda').'%')
            ->orWhere(function ($query) use ( $arreglo ) {
                foreach($arreglo as $a) {
                    $query->orWhere('id', $a);
                }
            })
            ->get();
        $objeto = [];
        $publicaciones = [];
        if(count($coleccion) > 0){
            foreach($coleccion as $c){
                $objeto = [
                    "id" => $c->id,
                    "tipoPublicacion" => $c->tipo_publicacion,
                    "autorPublicacion" => self::getNombre($c->autorPublicacion),
                    "mostrarContacto" => self::getDatos($c->autorPublicacion, $c->mostrar_contacto),
                    "fotoObjeto" => self::getImage($c->foto_objeto),
                    "descObjetoC" => $c->desc_objetoC,
                    "descDetallada" => $c->desc_detallada,
                    "lugar" => $c->lugar,
                    "statusPublicacion" => $c->statusPublicacion
                ];
                $publicaciones[] = $objeto;
            }
            return response()->json($publicaciones);
        }
        return abort(404);
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
        $publicacionesR = Publicacion::join('usuario_publicacion', 'publicaciones.id', '=', 'usuario_publicacion.id_publicacion')
        ->select('publicaciones.*', 'usuario_publicacion.mensaje', 'usuario_publicacion.id_usuarioR')
        ->where('usuario_publicacion.id_usuarioR', $id)
        ->get();
        $objeto = [];
        $objeto2 = [];
        $arreglo = [];
        $arreglo2 = [];
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
        foreach($publicacionesR as $p){
            $objeto2 = [
                "id" => $p->id,
                "tipoPublicacion" => $p->tipo_publicacion,
                "mostrarContacto" => $p->mostrar_contacto,
                "fotoObjeto" => self::getImage($p->foto_objeto),
                "descObjetoC" => $p->desc_objetoC,
                "descDetallada" => $p->desc_detallada,
                "autorPublicacion" => $p->autorPublicacion,
                "lugar" => $p->lugar,
                "statusPublicacion" => $p->statusPublicacion,
                "mensaje" => $p->mensaje,
                "idUsuarioR" => self::getNombre($p->id_usuarioR)
            ];
            $arreglo2[] = $objeto2;
        }
        return response()->json([$arreglo, $arreglo2], 200);
    }
}
