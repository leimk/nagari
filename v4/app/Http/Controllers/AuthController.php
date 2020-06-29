<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //

    public function register(Request $request)
    {
        $nama = $request->input('nama');
        $password = Hash::make($request->input('password'));

        $register = User::create([
                        'nama' => $nama,
                        'password' => $password
                    ]);
        
        if($register){
            return response()->json([
                'success'   =>  true,
                'message'   =>  'Berhasil Register User',
                'data'      =>  $register
            ],201);
        }else{
            return response()->json([
                'success'   =>  false,
                'message'   =>  'Gagal Register User',
                'data'      =>  ''
            ],400);    
        }
        
        // return response()->json([
        //     'message'   => $request->input('nama')
        // ],200);
        
    }
    public function login(Request $request)
    {
        $nama = $request->input('nama');
        $password = $request->input('password');

        $user = User::where('nama', $nama)->first();

        if(Hash::check($password, $user->password)) {
            $apiToken = base64_encode(str_random(40));

            $user->update([
                'apiToken' => $apiToken
            ]);

            return response()->json([
                'success'   =>  true,
                'message'   => 'Berhasil Login',
                'data'      =>  [
                                'user'  =>  $user,
                                'apiToken'  =>  $apiToken
                                ]
                ],201);
        } else{
            return response()->json([
                'success'   =>  false,
                'message'   => 'Gagal Login',
                'data'      =>  ''
            ],400);
        }


    }

    
}
