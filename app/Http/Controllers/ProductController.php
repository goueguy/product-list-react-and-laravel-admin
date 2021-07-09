<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'required|image|mimes:png,jpg,jpeg,gif',
            'description'=>'required|string',
            'price'=>"required|integer",
        ]);

        if($validator->fails()){
                return response()->json([
                    "error"=>$validator->errors(),
                    "code"=>400
                ]);
        }

        $newProduct = new Product();
        $newProduct->name = $request->input('name');
        $product = $request->file('image');
        if($request->hasFile('image')){

            $fileNameExtension = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameExtension, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $fileName."_".time().".".$extension;
            $request->file('image')->move('uploads',$fileNameToStore);
        }else{
            $fileNameToStore = "default.png";
        }

        $newProduct->file = $fileNameToStore;
        $newProduct->description = $request->input('description');
        $newProduct->slug = Str::slug($request->input('name'));
        $newProduct->price = $request->input('price');
        $newProduct->save();

        return response()->json([
            "message"=>"Product added",
            "data"=>$newProduct,
            "code"=>200
        ]);
    }
}

//12-Laravel with React Project # Add Product API with Image-tMLwZtnh5Xo
