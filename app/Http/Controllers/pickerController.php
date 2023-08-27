<?php

namespace App\Http\Controllers;

use App\Http\Requests\pickerRequest;
use App\Models\Order;
use App\Models\Picker;
use App\Traits\AuthorizedCheckTrait;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class pickerController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth.guard:pickers-api', ['except' => ['pickerRegister','pickerLogin']]);
//    }
    use AuthorizedCheckTrait;

    public  function showAllPicker(){
        $this->authorizCheck('showAllPicker');
        $picker= Picker::get();
        return response()->json(['message'=>'ok','data'=>$picker],200);
    }



    public  function pickerRegister(pickerRequest $request){
        try {
            $picker=new Picker();
            $picker=$this->processAdd($picker,$request);
            if ($picker) {
                return response(["message"=>"ok",'data'=>$picker],201);
            } else
                return response(["message"=>"error in validation"],404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public  function update(Request $request,$id){

        try {
            $picker=Picker::findOrFail($id);
            $picker = $this->process($picker, $request);
            if ($picker) {
                return response(['data'=>$picker,'message'=>'ok'],200);

            } else
                return response(['message'=>'not found'],404);
        }catch (\Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }


    }
    public function delete($id){
        try {
            $picker=Picker::find($id);

            if ($picker) {
                $picker->delete();
                return Response(["message"=>"ok"],200);
            } else
                return response("picker not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }


    }

    public function search($fullName,$phoneNum){
        try{
            $picker=Picker::where(['firstName'=>$fullName,'phoneNum'=>$phoneNum])->first();
            if ($picker) {

                return Response(["message"=>"ok",'data'=>$picker],200);
            } else
                return response("picker not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }



    protected function process(Picker $picker,Request $request){
        $picker->firstName=$request->firstName;
        $picker->lastName=$request->lastName;
        $picker->email=$request->email;
         $picker->password=Hash::make($request->password);
          $picker->phoneNum=$request->phoneNum;
         // $picker->accoutStatus=$request->accoutStatus;
          $picker->warehouseId=$request->warehouseId;
        $picker->save();
        return $picker;
    }

    protected function processAdd(Picker $picker,pickerRequest $request){
        $picker->firstName=$request->firstName;
        $picker->lastName=$request->lastName;
        $picker->email=$request->email;
        $picker->password=Hash::make($request->password);
        $picker->phoneNum=$request->phoneNum;
        // $picker->accoutStatus=$request->accoutStatus;
        $picker->warehouseId=$request->warehouseId;
        $picker->save();
        return $picker;
    }

    public function showAllUNAcceptPicker()
    {
        try {
            $picker=Picker::where(['accoutStatus'=>null])->get();
            if ($picker) {
                return response(["message"=>"ok",'data'=>$picker],201);
            } else
                return response(["message"=>"pickers are not found"],201);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function pickerLogin()
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth()->guard('pickers-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user=auth()->guard('pickers-api')->user();
        return Response(['data'=>$user,'token'=>$token]);
    }

    public function me()
    {
        $user=auth()->guard('pickers-api')->user();

        if ($user)
            return response()->json($user);
        return response()->json(['message'=>'not picker']);
    }


    public function pickerLogout()
    {
        auth()->guard('pickers-api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }


    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


    public  function accountAvailable(Request $request ,int $id){
        try {
            $this->authorizCheck('showAllPicker');
            $validated = $request->validate([
                'accoutStatus' => 'required|boolean',
            ]);
            $picker=Picker::find($id);
            $picker->accoutStatus=$request->accoutStatus;
            $picker->save();
            if ($picker) {
                return response(["message"=>"ok",'data'=>$picker],201);
            } else
                return response(["message"=>"error in validation"],404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//warehose
    public  function showPickerInWarehouse($orderId){
    $this->authorizCheck('showPickerInWarehouse');     //new
        $order= Order::find($orderId);
        $warehouses=$order->warehouse;
            $warehouses->load(['picker:id,firstName,lastName,warehouseId,countOrders']);
        return response()->json(['message'=>'ok','data'=>$warehouses],200);
    }


}
