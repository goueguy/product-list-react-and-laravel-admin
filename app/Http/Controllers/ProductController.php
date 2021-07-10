<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use App\Models\Product;
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

    public function list(){
        //$newProduct = Product::all();
        $newProduct = Product::orderByDesc('id')->paginate(4);
        if(!$newProduct){
            $message = [
                "message"=>"Product List Not Found",
                "data"=>[],
                "code"=>400
            ];
        }else{
            $message = [
                "message"=>"Product List Found",
                "data"=>$newProduct,
                "code"=>200
            ];
        }
        return response()->json($message);
    }

    public function delete($id){
        $product = Product::find($id);
        if(isset($product)){
            $product->delete();
            $image = public_path()."/uploads/".$product->file;

            if(file_exists($image)){
               File::delete($image);
            }
            return response()->json(
                [
                    "message"=>"Product is deleted",
                    "data"=>$product,
                    "code"=>200
                ]
            );
        }else{
             return response()->json(
                [
                    "message"=>"Operation Failed",
                    "data"=>[],
                    "code"=>400
                ]
            );
        }
    }

    public function edit($id){
        $product = Product::find($id);
        return response()->json(

            [
                "message"=>"Product Found",
                "data"=>$product,
                "code"=>200
            ]
        );
    }

    public function update(Request $request,$id){
        $product = Product::find($id);

       if($request->hasFile('image')){

            $fileNameExtension = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameExtension, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $fileName."_".time().".".$extension;
            $image= public_path()."/uploads/".$product->file;
                if(file_exists($image)){
                   File::delete($image);
                }
            $request->file('image')->move('uploads',$fileNameToStore);
        }else{
            $fileNameToStore = $product->file;
        }
        
        $data = [
            "name"=>$request->input('name'),
            "file"=>$fileNameToStore,
            "description"=>$request->input('description'),
            "slug"=>Str::slug($request->input('name')),
            "price"=>$request->input('price')
        ];

        $update = Product::where("id",$product->id)->update($data);
        if(!$update){
            return response()->json([
                "message"=>"Product updated failed",
                "data"=>[],
                "code"=>400
            ]);
        }else{

            return response()->json([
                "message"=>"Product updated",
                "data"=>$product,
                "code"=>200
            ]);
        }
    }

    public function search($key){

        $product = Product::where('name','LIKE',"%$key%")
        ->orWhere('price','LIKE',"%$key%")
        ->get();
        if(count($product)>0){
            return response()->json([
                "message"=>"Product Found",
                "data"=>$product,
                "code"=>200
            ]);
        }else{
            return response()->json([
                "message"=>"Product Not found",
                "data"=>[],
                "code"=>400
            ]);
        }
    }
}
