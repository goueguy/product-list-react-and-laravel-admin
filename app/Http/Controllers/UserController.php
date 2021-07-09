<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password'=>'required|string|min:8'
        ]);
        if($validator->fails()){
            return response()->json([
                "message"=>"User is not registered",
                "error"=>$validator->errors(),
                "code"=>400
            ]);
        }
        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return response()->json([
            "message"=>"User Registered",
            "data"=>$user,
            "code"=>200
        ]);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password'=>'required|string|min:8'
        ]);
        if($validator->fails()){
            return response()->json([
                "message"=>$validator->errors(),
                "code"=>400
            ]);
        }
        try {
            $user = User::where('email',$request->email)->first();
            if(!$user || !Hash::check($request->password, $user->password)){
                return response()->json(
                    [
                        "message"=>"Adresse Email ou Mot de passe incorrect",
                        "code"=>400
                    ]
                );
            }
            else{
                return response()->json(
                    [
                        "message"=>"Utilisateur Trouvé",
                        "data"=>$user,
                        "code"=>200
                    ]
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    "message"=>$e->getMessage(),
                    "code"=>400
                ]
            );
        }
    }
}
