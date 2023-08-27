<?php

namespace App\Http\Controllers;

use App\Http\Requests\warehouseRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;


class roleController extends Controller
{

    public function showAll()
    {
        $role= Role::get();
        return response()->json(['message'=>'ok','data'=>$role],200);
    }


    public  function add(Request $request){
        try {
            $role=new Role();
            $role=$this->process($role,$request);
            if ($role) {
                return response(["message"=>"ok",'data'=>$role],201);
            } else
                return response(["message"=>"error in add role"],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public  function update($id,Request $request){
        try {
            $role=Role::findOrFail($id);
            $role = $this->process($role, $request);
            if ($role) {
                return response(['data'=>$role,'message'=>'ok'],200);
            } else
                return response(['message'=>'the role not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function delete($id){
        try {
            $role=Role::find($id);
            if ($role) {
                $role->delete();
                return Response(["message"=>"ok"],200);
            } else
                return response("role not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    protected function process(Role $role,Request $request){
        $role->name=$request->name;
        $role->save();
        return $role;
    }

    public function view($id){
        try {
            $role=Role::find($id);
            if ($role) {

                return response()->json(['data'=>$role],200);
            } else
                return response("role not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function search($name){
        try{
            $role=Role::where(['name'=>$name])->first();
            if ($role) {

                return Response(["message"=>"ok",'data'=>$role],200);
            } else
                return response("role not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function viewPermissinInRole($id){
        try {
            $role=Role::find($id);
            $role= $role->permission;
            if ($role) {
                return response()->json(['data'=>$role],200);
            } else
                return response("role not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function addPermissionToRole($idPermission,$idRole){
        try {
            $permission=Permission::find($idPermission);
            if(!$permission){
                return response(["data"=>"permission not found"]);
            }
            $role=Role::where(['id'=>$idRole])->first();
                $role->permission()->attach($idPermission);

            //            if(!$role) {
//                $rolec = new roleController();
//                $productController->add($request);
//
//            }
//            Stock::create([
//                'quantity'=>$quantity,
//                'productId'=>$product->id,
//                'warehouseId'=>$warehouse->id,
//            ]);
            return response(["message"=>"ok",'data'=>$role],201);

        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
}
