<?php

namespace App\Http\Controllers;

use App\Http\Requests\warehouseRequest;
use App\Models\Product;
use App\Models\Warehouse;
use App\Traits\AuthorizedCheckTrait;
use Exception;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    use AuthorizedCheckTrait;
    public function showAll()
    {
        $wareHouse= Warehouse::get();
        return response()->json(['message'=>'ok','data'=>$wareHouse],200);
    }


    public  function add(warehouseRequest $request){
        $this->authorizCheck('warehouseAdd');
        try {
            $warehouse=new Warehouse();
            $warehouse=$this->processAdd($warehouse,$request);
            if ($warehouse) {
                return response(["message"=>"ok",'data'=>$warehouse],201);
            } else
                return response(["message"=>"error in add warehouse"],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public  function update($id,Request $request){
        try {
            $warehouse=Warehouse::findOrFail($id);
            $warehouse = $this->process($warehouse, $request);
            if ($warehouse) {
                return response(['data'=>$warehouse,'message'=>'ok'],200);
            } else
                return response(['message'=>'the warehouse not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function delete($id){
        try {
            $warehouse=Warehouse::find($id);
            if ($warehouse) {
                $warehouse->delete();
                return Response(["message"=>"ok"],200);
            } else
                return response("Warehouse not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    protected function process(Warehouse $warehouse,Request $request){
        $warehouse->name=$request->name;
        $warehouse->city=$request->city;
        $warehouse->address=$request->address;
        $warehouse->save();
        return $warehouse;
    }

    protected function processAdd(Warehouse $warehouse,warehouseRequest $request){
        $warehouse->name=$request->name;
        $warehouse->city=$request->city;
        $warehouse->address=$request->address;
        $warehouse->save();
        $products=Product::get();
        foreach ($products as $product)
            $warehouse->product()
                ->where('warehouseId','=', $warehouse->id)
                ->attach($product,[
                    'stock.quantity'=>0
                ]);
        return $warehouse;
    }

    public function view($id){
        try {
            $warehouse=Warehouse::find($id);
            if ($warehouse) {

                return response()->json(['data'=>$warehouse],200);
            } else
                return response("Warehouse not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function search($name){
        try{
            $warehouse=Warehouse::where(['name'=>$name])->first();
            if ($warehouse) {

                return Response(["message"=>"ok",'data'=>$warehouse],200);
            } else
                return response("warehouse not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function viewProductWarehouse($id){
        try {
            $warehouse=Warehouse::find($id);
            $warehouse= $warehouse->product;
            if ($warehouse) {
                return response()->json(['data'=>$warehouse],200);
            } else
                return response("Warehouse not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
}
