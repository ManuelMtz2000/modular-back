<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
        $user->tipo_usuario_id = 1;
        $user->curp = $request->input('curp');
        $user->datosContacto = $request->input('datosContacto');
        if($request->hasFile('imagen')){
            $file = $request->file('imagen');
            $filename = $file->getClientOriginalName();
            $picture = date('d-m-y h.i.s A').'-'.$filename;
            $user->foto_identificacion = $picture;
            $file->move(storage_path('identificaciones'), $picture);
        }
        if($request->hasFile('perfil')){
            $file = $request->file('perfil');
            $filename = $file->getClientOriginalName();
            $picture = date('d-m-y h.i.s A').'-'.$filename;
            $user->foto_perfil = $picture;
            $file->move(storage_path('fotos_p'), $picture);
        }
        $user->save();
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('Token')->plainTextToken
        ], 200);
    }

    public function login(Request $request){
        $user = User::where('correo', $request->input('correo'))->first();
        $objeto = [
            "id" => $user->id,
            "nombre" => $user->nombre,
            "correo" => $user->correo,
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
    }

    public function getPerfil($id){
        $user = User::where('id', $id)->first();
        return 'http://localhost:8000/img/fotos_p/'.$user->foto_perfil;
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
