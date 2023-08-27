<?php

namespace App\Http\Controllers;



use App\Http\Requests\customerAcceptRequest;
use App\Http\Requests\customerRequest;
use App\Models\Customer;
use App\Models\Map;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\Truck;
use App\Traits\AuthorizedCheckTrait;
use Exception;
use http\Env\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class cuctomerController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth.guard:customer-api', ['except' => ['Register','Login']]);
//    }

use AuthorizedCheckTrait;
    public  function showAllCustomer(){
        $this->authorizCheck('showAllCustomer');
        $customer= Customer::get();
        return response()->json(['message'=>'ok','data'=>$customer],200);
    }

    public  function Register(customerRequest $request){
        try {
            $customer=new Customer();
            $customer=$this->processAdd($customer,$request);
            if ($customer) {
                return response(["message"=>"ok",'data'=>$customer],201);
            } else
                return response(["message"=>"error in validation"],404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function update(Request $request,$id){
        try {
            $customer=Customer::findOrFail($id);
            $customer = $this->process($customer, $request);
            if ($customer) {
                return response(['data'=>$customer,'message'=>'ok'],200);

            } else
                return response(['message'=>'not found'],404);
        }catch (\Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }


    }
    public function delete($id){
        $this->authorizCheck('customerDelete');
        try {
            $customer=Customer::find($id);

            if ($customer) {
                $customer->delete();
                return Response(["message"=>"ok"],200);
            } else
                return response("customer not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }


    }

    public function search($fullName,$phoneNum){
        $this->authorizCheck('searchCustomer');
        try{
            $customer=Customer::where(['firstName'=>$fullName,'phoneNum'=>$phoneNum])->first();
            if ($customer) {

                return Response(["message"=>"ok",'data'=>$customer],200);
            } else
                return response("customer not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    protected function process(Customer $customer,Request $request){
        $customer->firstName=$request->firstName;
        $customer->lastName=$request->lastName;
        $customer->email=$request->email;
        $customer->password=Hash::make($request->password);
        $customer->phoneNum=$request->phoneNum;
         // $picker->accoutStatus=$request->accoutStatus;
       // $customer->warehouseId=$request->warehouseId;
        $customer->save();
        return $customer;
    }

    protected function processAdd(Customer $customer,customerRequest $request){
        $customer->firstName=$request->firstName;
        $customer->lastName=$request->lastName;
        $customer->email=$request->email;
        $customer->password=Hash::make($request->password);
        $customer->phoneNum=$request->phoneNum;
        $customer->latitude=$request->latitude;
        $customer->longitude=$request->longitude;
        // $picker->accoutStatus=$request->accoutStatus;
        // $customer->warehouseId=$request->warehouseId;
        $customer->save();
        return $customer;
    }

    public function showAllUNAcceptCustomer()
    {
        $this->authorizCheck('showAllUNAcceptCustomer');
        try {

            $customer=Customer::where(['status'=>null])->get();
            if ($customer) {
                return response(["message"=>"ok",'data'=>$customer],201);
            } else
                return response(["message"=>"customer are not found"],201);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function showAllAcceptCustomer()
    {
        $this->authorizCheck('showAllUNAcceptCustomer');
        try {

            $customer=Customer::where(['status'=>1])->get();
            if ($customer) {
                return response(["message"=>"ok",'data'=>$customer],201);
            } else
                return response(["message"=>"customer are not found"],201);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
    public  function accountAvailable(Request $request ,int $id){
        $this->authorizCheck('customerAccountAvailable');
        try {
            $validated = $request->validate([
                'accountStatus' => 'required|boolean',
            ]);
            $customer=Customer::find($id);
            $customer->accountStatus=$request->accountStatus;
            $customer->save();
            if ($customer) {
                return response(["message"=>"ok",'data'=>$customer],201);
            } else
                return response(["message"=>"error in validation"],404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public  function accept(customerAcceptRequest  $request ,int $id){
        $this->authorizCheck('customerLink');
        try {
            $customer=Customer::find($id);
            if ($customer) {
            if ($customer->warehouseId == null){
            $customer->paymentMethod=$request->paymentMethod;
            $customer->status=1;
                $customer->accountStatus=1;
            $customer->warehouseId=$request->warehouseId;
            $customer->save();
                return response(["message"=>"ok",'data'=>$customer],201);
            }else
                return response(["message"=>"customer is linked with warehouse"],404);
            }
            else
                return response(["message"=>"not found"],404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
#####################################################################################
    public  function reject(Request  $request ,int $id)
    {
$this->authorizCheck('CustomerReject');
        try {
            $customer=Customer::find($id);
            if ($customer) {
                if ($customer->status == null){
                     $customer->status=0;
                     $customer->accountStatus=0;
                     $customer->description=$request->description;
                     $customer->save();
                    return response(["message"=>"ok",'data'=>$customer],201);
                }
                elseif($customer->status == 0)
                    return response(["message"=>"customer is rejected already"],404);
            }
            else
                return response(["message"=>"not found"],404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function Login()
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth()->guard('customer-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user=auth()->guard('customer-api')->user();
        return Response(['data'=>$user,'token'=>$token]);
    }

    public function me()
    {
//        $this->authorizCheck('customerProfile');
        $user=auth()->guard('customer-api')->user();
        if ($user)
            return response()->json($user);
        return response()->json(['message'=>'not customer']);
    }


    public function Logout()
    {
        auth()->guard('customer-api')->logout();
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


    public function orderCustomersNotDone()
    {
        $user=auth()->guard('customer-api')->user();
        if ($user) {

            $order=$user->order()->where('currentStatus','!=','complete')->get();
            return response()->json(['message'=>$order]);
        }
        return response()->json(['message'=>'not customer']);
    }


    public function  orderStatuse($orderId){
        try {
            $customer=auth()->guard('customer-api')->user();
            if ($customer){
                $order=Order::find($orderId);
            $messages[]=null;
            if ($order) {
                if ($order->currentStatus=='salesManager'&& $order->checkProduct==1){
                    $items=$order->product;
                    foreach($items as $item){
                        $product=Product::find($item->id);
                        if($order->product()
                            ->where('productId', $product->id)
                            ->wherePivot('message', '!=', null)
                            ->exists())
                            $messages=$item;

                    }
                    return response()->json(['data'=>$messages],200);
                }
                else
                    return response()->json(['orderStatuse'=>$order->currentStatus,"data"=>$order],200);

            } else
                return response("order not found",404);
        }}catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function customerISProduct(){
        try {
            $customer=auth()->guard('customer-api')->user();
           $product= $customer->warehouse->product->load(['category:id,nameCategory']);
            if($product){

                return response(['data'=>$product,'message'=>'ok'],200);
            } else
                return response(['message'=>'the product not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function storeMap(Request $request){
        try {
            $user=auth()->guard('customer-api')->user();
            $map=new Map();
            $map->latitude=$request->latitude;
            $map->longitude=$request->longitude;
            $map->customerId=$user->id;
            $map->save();
            if ($map) {
                return response(["message" => "ok"], 201);
            }else
                return response(["message" => "error in validation"], 404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function showAllLocation($id)
    {
        $order=Order::find($id);
       $truck= $order->shipping->truck;
        if ($truck) {
                return response()->json(['message' => 'ok', 'data' => $truck], 200);
        }else
            return response()->json(['message' => 'no location found '], 404);
    }


}
