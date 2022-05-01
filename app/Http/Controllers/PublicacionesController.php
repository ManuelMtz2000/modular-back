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
            $user = User::find($p->autorPublicacion);
            $categoria = Categoria::find($p->categoriasPublicacion);
            $objeto = [
                "id" => $p->id,
                "tipoPublicacion" => $p->tipo_publicacion,
                "categoriasPublicacion" => $categoria->nombre_categoria,
                "autorPublicacion" => $user->nombre,
                "idAutor" => $p->autorPublicacion,
                "fotoUsuario" => self::getPerfil($p->autorPublicacion),
                "mostrarContacto" => $p->mostrar_contacto == 'Si' ? $user->datosContacto : null,
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

    // public function codigoCorreo(Request $request){
    //     $user = User::where('curp', $request->input('codigo'))->get();
    //     if( count($user) > 0 ) {
    //         $userP = User::where('id', $request->input('id'))->first();
    //         $usuariorp = new UsuarioPublicacion();
    //         $usuariorp->id_usuarioR = $user[0]->id;
    //         $usuariorp->id_usuarioP = $userP->id;
    //         $usuariorp->
    //     }
    // }

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
        $request->validate([
            'imagen' => 'required|image|mimes:png,jpg,jpeg'
        ]);
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
            $etiqueta = self::eliminar_acentos($e);
            Storage::disk('local')->append('extravios.pl', PHP_EOL.'etiquetas('.$publicacion->id.', '.$etiqueta.').', null);
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
        $user = User::find($publicacionColeccion->autorPublicacion);
        $objeto = [];
            $objeto = [
                "id" => $publicacionColeccion->id,
                "tipoPublicacion" => $publicacionColeccion->tipo_publicacion,
                "autorPublicacion" => $user->nombre,
                "mostrarContacto" => $publicacionColeccion->mostrar_contacto == 'Si' ? $user->datosContacto : null,
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
        $userReporta = DB::table('reportes')->where('id_usuario_reporta', $request->input('idReporta'))->where('id_publicacion', $publicacion->id)->get();
        if(count($userReporta) >= 1) {
            return abort(404);
        } else {
            $user = DB::table('reportes')->where('id_usuario_reportado', $publicacion->autorPublicacion)->get();
            if(count($user) < 3) {
                DB::table('reportes')->insert([
                    "id_usuario_reporta" => $request->input('idReporta'),
                    "id_usuario_reportado" => $publicacion->autorPublicacion,
                    "id_publicacion" => $publicacion->id,
                    "descripcion" => $request->input('descripcion')
                ]);
            } else {
                DB::table('users')->where('id', $publicacion->autorPublicacion)->update([
                    "tipo_usuario_id" => 3
                ]);
                DB::table('publicaciones')->where('autorPublicacion', $publicacion->autorPublicacion)->update([
                    "statusPublicacion" => 2
                ]);
            }
        }
    }

    function eliminar_acentos($cadena){
		
		//Reemplazamos la A y a
		$cadena = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
		$cadena
		);

		//Reemplazamos la E y e
		$cadena = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$cadena );

		//Reemplazamos la I y i
		$cadena = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$cadena );

		//Reemplazamos la O y o
		$cadena = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$cadena );

		//Reemplazamos la U y u
		$cadena = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$cadena );

		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$cadena
		);
		
		return $cadena;
	}

    public function search(Request $request){
        $array = [];
        $output = '';
        $coleccion = [];
        if($request->input('busquedaArray')){
            $array = $request->input('busquedaArray');
            foreach($array as $a){
                $palabra = str_replace(' ', '', strtolower($a));
                $palabra2 = self::eliminar_acentos($palabra);
                $output = $output.`swipl -s C:\Users\Manue\OneDrive\Escritorio\Modular\\extravios-cucei-back\storage\app\\extravios.pl -g "buscar({$palabra2})" -t halt.`;
            }
            $arreglo = str_split($output);
            $coleccion = Publicacion::where('statusPublicacion', 1)
            ->where(function ($query) use ( $array, $arreglo ) {
                foreach($array as $a) {
                    $query->orWhere('desc_objetoC', $a)
                    ->orWhere('desc_detallada', $a)
                    ->orWhere(function ($query2) use ( $arreglo ) {
                        foreach($arreglo as $a2){ 
                            $query2->orWhere('id', $a2);
                        }
                    });
                }
            })
            ->where('statusPublicacion', 1)
            ->get();
        } else {
            $palabra = explode(" " ,$request->input('busqueda'));
            foreach($palabra as $p){
                $output = $output.`swipl -s C:\Users\Manue\OneDrive\Escritorio\Modular\\extravios-cucei-back\storage\app\\extravios.pl -g "buscar({$p})" -t halt.`;
            }
            $arreglo = preg_split('{,}',$output);
            $coleccion = Publicacion::where('statusPublicacion', 1)
            ->where(function ($query) use ( $arreglo, $request ) {
                $query->where('desc_objetoC', 'like', '%'.$request->input('busqueda').'%')
                ->orWhere('desc_detallada', 'like', '%'.$request->input('busqueda').'%')
                ->orWhere('lugar', 'like', '%'.$request->input('busqueda').'%')
                ->orWhere(function ($query) use ( $arreglo ) {
                    foreach($arreglo as $a) {
                        $query->orWhere('id', $a);
                    }
                });
            })
            ->get();
        }
        $objeto = [];
        $publicaciones = [];
        if(count($coleccion) > 0){
            foreach($coleccion as $c){
                $user = User::find($c->autorPublicacion);
                $objeto = [
                    "id" => $c->id,
                    "tipoPublicacion" => $c->tipo_publicacion,
                    "autorPublicacion" => $user->nombre,
                    "mostrarContacto" => $c->mostrar_contacto == 'Si' ? $user->datosContacto : null,
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

    public function busquedaInteligente(Request $request){
        $picture = '';
        $filename = '';
        if($request->hasFile('imagen')){
            $file = $request->file('imagen');
            $filename = $file->getClientOriginalName();
            $picture = date('d-m-y h.i.s A').'-'.$filename;
            $filename = pathinfo($picture, PATHINFO_FILENAME);
            $file->move(public_path('busquedas'), $picture);
        }

        $file_path = public_path('busquedas\\'.$picture);
        $api_credentials = array(
            'key' => 'acc_49281df14ac8a2b',
            'secret' => '45e83c2dd5b8c3c593c07cbb156d3694'
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.imagga.com/v2/tags?language=es&limit=5");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERPWD, $api_credentials['key'].':'.$api_credentials['secret']);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);

        // dd($file_path, $filename, $picture);

        $fields = [
            'image' => new \CurlFile($file_path, $filename.'/jpeg', $picture)
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $json_response = json_decode($response);
        return response()->json($json_response);
    }
}
