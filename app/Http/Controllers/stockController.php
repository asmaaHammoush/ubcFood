<?php

namespace App\Http\Controllers;

use App\Http\Requests\productRequest;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Traits\AuthorizedCheckTrait;
use Exception;
use Illuminate\Http\Request;

class stockController extends Controller
{

//    public function showAll()
//    {
//        $product= ::get();
//        return response()->json(['message'=>'ok','data'=>$product],200);
//    }

    use AuthorizedCheckTrait;

//    public  function add(Request $request, $warehousId){
//
//        try {
//            $warehouse=Warehouse::find($warehousId);
//            $product=Product::find($request->productId);
//            if(!$warehouse
//                ->product()
//                ->where('productId', $request->productId)
//                ->exists()){
//            $warehouse->product()->attach($product,[
//               'quantity'=> $request->quantity,
//                ]
//            );
//                return response(["message"=>"ok",'data'=>$warehouse],201);
//            }else
//                return response(["message"=>"product is found in this warehouse"],201);
//
//        }catch (Exception $ex){
//            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
//        }
//    }
//warehouse viewStock
    public function view($id){
        $this->authorizCheck('viewStock');
        try {
            $warehouse=Warehouse::find($id);
            $warehouse=$warehouse->product;
            if ($warehouse) {
                return response()->json(['data'=>$warehouse],200);
            } else
                return response("Warehouse not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function updateQuantity(Request $request, $warehousId){
        $this->authorizCheck('updateQuantity');
        try {
            $warehouse=Warehouse::find($warehousId);
            $product=Product::find($request->productId);
            if($warehouse
                ->product()
                ->where('productId', $request->productId)
                ->exists()){

                $q= $warehouse
                    ->product()
                    ->where('productId',$product->id)
                    ->select('stock.quantity')
                    ->value('stock.quantity');

                $warehouse->product()->updateExistingPivot($product,[
                    'quantity'=>$q+$request->quantity,
                ]);

                return response(["message"=>"ok",'data'=>$warehouse],201);
            }else
                return response(["message"=>"product is found in this warehouse"],201);

        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
}

