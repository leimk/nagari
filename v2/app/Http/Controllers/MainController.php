<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class MainController extends Controller
{
    //

    public function register(Request $request){
      $request-> validate([
          'name'=> 'required|string',
          'email'=>'required|string|email||unique:users',
          'password'=>'required|string|confirmed'
      ]);

      $user = new User([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=> bcrypt($request->password)
      ]);
    }
}
