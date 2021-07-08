<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
class UserController extends Controller
{
    function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password'=>'required|string|min:8'
        ]);
        if($validator->fails()){
            return response([
                "message"=>"User is not registered",
                "error"=>$validator->errors(),
                "code"=>404
            ]);
        }
        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return response([
            "message"=>"User Registered",
            "data"=>$user,
            "code"=>200
        ]);
    }
}
