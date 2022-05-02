<?php

namespace App\Http\Controllers;

use App\Mail\Verificar as MailVerificar;
use App\Models\User;
use App\Models\Verificar;
use Branca\Branca;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $user = new User();
        $user->nombre = $request->input('nombre');
        $user->correo = $request->input('correo');
        $user->contrasenia = Hash::make($request->input('contrasenia'));
        $user->tipo_usuario_id = (int)$request->input('tipo_usuario_id');
        $user->curp = $request->input('curp');
        $user->datosContacto = $request->input('datosContacto');
        if($request->hasFile('imagen')){
            $file = $request->file('imagen');
            $filename = $file->getClientOriginalExtension();
            $picture = date('d-m-y_h.i.s_A').'-'.Str::random(8).'.'.$filename;
            $user->foto_identificacion = $picture;
            $file->move(storage_path('identificaciones'), $picture);
        }
        if($request->hasFile('perfil')){
            $file = $request->file('perfil');
            $filename = $file->getClientOriginalExtension();
            $picture = date('d-m-y_h.i.s_A').'-'.Str::random(8).'.'.$filename;
            $user->foto_perfil = $picture;
            $file->move(public_path('img/fotos_p'), $picture);
        } else {
            $user->foto_perfil = "Default.jpeg";
        }
        $user->save();
        $verificar = new Verificar();
        $verificar->id_usuario = $user->id;
        $verificar->codigo = Str::random(6);
        $verificar->save();
        Mail::to($request->input('correo'))->send(new MailVerificar($verificar));
        $objeto = [
            "id" => $user->id,
            "nombre" => $user->nombre,
            "correo" => $user->correo,
            "verificado" => $user->email_verified_at,
            "fotoP" => self::getPerfil($user->id)
        ];
        return response()->json([
            'user' => $objeto,
            'token' => $user->createToken('Token')->plainTextToken
        ], 200);
    }

    public function verificar(Request $request, $id){
        $verificar = Verificar::where('id_usuario', $id)->where('codigo', $request->input('codigo'))->get();
        if(count($verificar) > 0){
            DB::table('users')->where('id', $id)->update(
                ["email_verified_at" => Carbon::now()]
            );
        } else {
            return abort(404);
        }
        $user = User::where('id', $id)->first();
        $objeto = [
            "id" => $user->id,
            "nombre" => $user->nombre,
            "correo" => $user->correo,
            "verificado" => $user->email_verified_at,
            "fotoP" => self::getPerfil($user->id)
        ];
        return response()->json($objeto);
    }

    public function login(Request $request){
        $userValidate = User::where('correo', $request->input('correo'))->where('tipo_usuario_id','!=',3)->get();
        if(count($userValidate) > 0){
            $user = User::where('correo', $request->input('correo'))->where('tipo_usuario_id','!=',3)->first();
            $objeto = [
                "id" => $user->id,
                "nombre" => $user->nombre,
                "correo" => $user->correo,
                "verificado" => $user->email_verified_at,
                "fotoP" => self::getPerfil($user->id)
            ];
            if(!Hash::check($request->input('contrasenia'), $user->contrasenia)){
                return response()->json([
                    'msg' => 'Datos incorrectos'
                ], 401);
            }
            return response()->json([
                'user' => $objeto,
                'token' => $user->createToken('Token')->plainTextToken
            ]);
        } else {
            return abort(404);
        }
    }

    public function siiau($codigo, $password)
    {
        $curl = curl_init();
        $url_consulta = 'https://sigi-login.cucei.udg.mx/validar-credenciales';
        $branca = new Branca(env('TOKEN_KEY', ''));
        //dd(env('SIGI_LOGIN_SISTEMA  ', ''));
        $token = $branca->encode(env('TOKEN_API_LOGIN_SIGI', ''));
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url_consulta,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'sistema:' . env('SIGI_LOGIN_SISTEMA'),
                'token:' . $token,
                'codigo:' . $codigo,
                'password:' . $password
            )
            ));

        return json_decode(curl_exec($curl) === 'true');
    }

    public function loginSiiau(Request $request){
        $user = User::where('curp', $request->input('codigo'))->first();
        $objeto = [
            "id" => $user->id,
            "nombre" => $user->nombre,
            "correo" => $user->correo,
            "verificado" => $user->email_verified_at,
            "fotoP" => self::getPerfil($user->id)
        ];
        return response()->json([
            'user' => $objeto,
            "token" => $user->createToken('Token')->plainTextToken
        ]);
    }

    public function verificarSiiau(Request $request){
        $verificar = User::where('curp', $request->input('codigo'))->get();
        if(count($verificar) > 0){
            return response()->json(true);
        } else {
            return response()->json([
                'msg' => 'Datos incorrectos'
            ]);
        }
    }

    public function getPerfil($id){
        $user = User::where('id', $id)->first();
        return 'http://192.168.193.13:8000/img/fotos_p/'.$user->foto_perfil;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        $userC = User::where('id', $user)->first();
        $objeto = [
            "id" => $userC->id,
            "nombre" => $userC->nombre,
            "datosContacto" => $userC->datosContacto,
            "fotoP" => self::getPerfil($userC->id)
        ];
        
        return response()->json($objeto, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user)
    {
        
    }

    public function validarStatus($id)
    {
        $user = User::where('id', $id)->where('tipo_usuario_id', '!=', 3)->get();
        if(count($user) > 0) {
            return response()->json($user);
        }
        return abort(404);
    }

    public function nuevaContra(Request $request, $id){
        $user = User::where('id', $id)->first();
        if(Hash::check($request->input('contraOld'), $user->contrasenia)){
            DB::table('users')->where('id', $id)->update([
                "contrasenia" => Hash::make($request->input('contraNew'))
            ]);
        } else {
            abort(404);
        }
    }

    public function editDatos(Request $request, $id){
        DB::table('users')->where('id', $id)->update([
            "datosContacto" => $request->input('datos')
        ]);
    }

    public function editFoto(Request $request, $id){
        $user = User::where('id', $id)->first();
        if($user->foto_perfil != "Default.jpeg"){
            File::delete(public_path('img/fotos_p/'.$user->foto_perfil));
        }
        if($request->hasFile('imagen')){
            $file = $request->file('imagen');
            $filename = $file->getClientOriginalExtension();
            $picture = date('d-m-y_h.i.s_A').'-'.Str::random(8).'.'.$filename;
            DB::table('users')->where('id', $id)->update([
                "foto_perfil" => $picture
            ]);
            $file->move(public_path('img/fotos_p'), $picture);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
