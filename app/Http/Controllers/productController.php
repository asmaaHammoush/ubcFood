<?php

namespace App\Http\Controllers;


use App\Http\Requests\productRequest;
use App\Models\Product;
use App\Models\Warehouse;
use App\Traits\AuthorizedCheckTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class productController extends Controller
{

//    public function __construct()
//    {
//        $this->middleware('auth:employee-api', ['except' => ['Register','Login']]);
//    }
   use AuthorizedCheckTrait;
    public function showAll()
    {
       $this->authorizCheck('showAllproduct');
        $products= Product::get();
        foreach ($products as $product){
            $product->category;
        }
        return response()->json(['message'=>'ok','data'=>$products],200);
    }


    public  function add(productRequest $request){
        $this->authorizCheck('productAdd');
        try {
            $product=new Product();
            $product=$this->processAdd($product,$request);
            if ($product) {
                return response(["message"=>"ok",'data'=>$product],201);
            } else
                return response(["message"=>"error in add product"],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public  function update($id,Request $request){
       $this->authorizCheck('productUpdate');
        try {
            $product=Product::findOrFail($id);
            $product = $this->process($product, $request);
            if ($product) {
                return response(['data'=>$product,'message'=>'ok'],200);
            } else
                return response(['message'=>'the product not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function delete($id){
       $this->authorizCheck('productDelete');
        try {
            $product=Product::find($id);
            if ($product) {
                $product->delete();
                return Response(["message"=>"ok"],200);
            } else
                return response("product not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    protected function processAdd(Product $product,productRequest $request){
        $product->name=$request->name;

        $elements = explode('\\', $request->image);
        $lastElement = end($elements);
        $product->image = $lastElement;

//        $image = $request->image;
//        $imageName = time() . '.' . $image->getClientOriginalExtension();
//        $image->move(public_path('Product'), $imageName);
//        $product->image=$imageName;
        $product->weight=$request->weight;
        $product->price=$request->price;
        $product->available=$request->available;
         $product->description=$request->description;
        $product->categorytId=$request->categorytId;
        $product->code=Str::random(5);
        $warehouses=Warehouse::get();
        $product->save();
        foreach ($warehouses as $warehouse)
            $product->warehouse()
                ->where('productId','=', $product->id)
                ->attach($warehouse, [
                'stock.quantity' => 0,
            ]);
        return $product;
    }

    protected function process(Product $product,Request $request){
        $product->name=$request->name;
        $product->image=$request->image;
//        $image = $request->image;
//        $imageName = time() . '.' . $image->getClientOriginalExtension();
//        $image->move(public_path('Product'), $imageName);
//        $product->image=$imageName;
        $product->weight=$request->weight;
        $product->price=$request->price;
        $product->available=$request->available;
        $product->description=$request->description;
        $product->categorytId=$request->categorytId;
        $product->save();
        return $product;
    }

    public function view($id){
        try {
            $product=Product::find($id);
            if ($product) {

                return response()->json(['data'=>$product],200);
            } else
                return response("product not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function viewProductAvailable(){
        try {
            $products=Product::where('available',1)->get();
            if ($products) {

                return response()->json(['data'=>$products],200);
            } else
                return response("not exit product available",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }



    public function search($name){
        try{
            $product=Product::where(['name'=>$name])->first();
            if ($product) {

                return Response(["message"=>"ok",'data'=>$product],200);
            } else
                return response("product not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function  availableProduct(Request $request,$productId){
        $this->authorizCheck('availableProduct');
        try {
            $validated = $request->validate([
                'available' => 'required|boolean',
            ]);
            $product=Product::find($productId);
            $product->available=$request->available;

            $product->save();
            if ($product) {
                return response()->json(['data'=>$product],200);
            } else
                return response("Product not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function showProductFinal(){
        try {
//            $customer=auth()->guard('customer-api')->user();
//            $product= $customer->warehouse->
            $products=Product::where('available',1);
            $product=$products
                ->latest('created_at')
                ->take(5)
                ->with('category:id,nameCategory')
                ->get();
            if($product){
                return response(['data'=>$product,'message'=>'ok'],200);
            } else
                return response(['message'=>'the product not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function showProductTrending(){
        try {
            $products=Product::where('available',1);
            $topProducts = $products->orderBy('trending', 'desc')->take(10)->get();
            if($topProducts){
                return response(['data'=>$topProducts,'message'=>'ok'],200);
            } else
                return response(['message'=>'the product not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
}
