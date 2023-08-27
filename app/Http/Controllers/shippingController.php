<?php

namespace App\Http\Controllers;


namespace App\Http\Controllers;

use App\Http\Requests\pickerRequest;
use App\Http\Requests\shippingRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Picker;
use App\Models\Shipping;
use App\Models\Truck;
use App\Models\Warehouse;
use App\Models\Map;

use Exception;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class shippingController extends Controller
{

//    public function __construct()
//    {
//        $this->middleware('auth.guard:shipping-api', ['except' => ['Register','Login']]);
//    }

    public function showAllShipping()
    {

        $Shipping = Shipping::get();
        return response()->json(['message' => 'ok', 'data' => $Shipping], 200);
    }

    public function Register(shippingRequest $request)
    {
        try {
            $Shipping = new Shipping();
            $Shipping = $this->processAdd($Shipping, $request);
            if ($Shipping) {
                return response(["message" => "ok", 'data' => $Shipping], 201);
            } else
                return response(["message" => "error in validation"], 404);
        } catch (Exception $ex) {

            return response(['data' => $ex->getMessage(), 'message' => 'error'], 400);
        }
    }

    public  function update(Request $request,$id){

        try {

            $Shipping=Shipping::findOrFail($id);
            $Shipping = $this->process($Shipping, $request);
            if ($Shipping) {
                return response(['data'=>$Shipping,'message'=>'ok'],200);

            } else
                return response(['message'=>'not found'],404);
        }catch (\Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }


    }
    public function delete($id){
        try {
            $Shipping=Shipping::find($id);

            if ($Shipping) {
                $Shipping->delete();
                return Response(["message"=>"ok"],200);
            } else
                return response("Shipping not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }


    }

    public function search($firstName,$lastName){

        try{
            $Shipping=Shipping::where(['firstName'=>$firstName,'lastName'=>$lastName])->first();
            if ($Shipping) {

                return Response(["message"=>"ok",'data'=>$Shipping],200);
            } else
                return response("Shipping not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    protected function process(Shipping $Shipping,Request $request)
    {

        $Shipping->firstName = $request->firstName;
        $Shipping->lastName = $request->lastName;

        $Shipping->email = $request->email;
        $Shipping->password = Hash::make($request->password);
       // $Shipping->accoutStatus = $request->accoutStatus;
        $Shipping->warehouseId = $request->warehouseId;
        $Shipping->save();

        return $Shipping;
    }

    protected function processAdd(Shipping $Shipping,shippingRequest $request)
    {

        $Shipping->firstName = $request->firstName;
        $Shipping->lastName = $request->lastName;

        $Shipping->email = $request->email;
        $Shipping->password = Hash::make($request->password);
        // $Shipping->accoutStatus = $request->accoutStatus;
        $Shipping->warehouseId = $request->warehouseId;
        $Shipping->save();

        return $Shipping;
    }

    public function Login()
    {
        $credentials = request(['password','email']);
        //$user=auth()->guard('pickers-api')->user();
        if (!$token = auth()->guard('shipping-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user=auth()->guard('shipping-api')->user();
        return Response(['data'=>$user,'token'=>$token]);
    }

    public  function accountAvailable(Request $request ,int $id){
        try {
            $validated = $request->validate([
                'accoutStatus' => 'required|boolean',
            ]);
            $shipping=Shipping::find($id);
            $shipping->accoutStatus=$request->accoutStatus;
            $shipping->save();
            if ($shipping) {
                return response(["message"=>"ok",'data'=>$shipping],201);
            } else
                return response(["message"=>"error in validation"],404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function me()
    {
        return response()->json(auth()->guard('shipping-api')->user());
    }


    public function Logout()
    {
        auth()->guard('shipping-api')->logout();
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
//warehouse  viewShippingInWarehouse
    public  function viewShippingInWarehouse($orderId){

        $order= Order::find($orderId);
        $shippings=$order->shipping;
        $warehouses=$order->warehouse;
        $warehouses->load(['shipping:id,firstName,lastName,warehouseId,countOrders']);
        return response()->json(['message'=>'ok','data'=>$warehouses],200);
    }
//    public  function viewShippingInWarehouse(int $id){
//        try {
//            $warehouse=Warehouse::find($id);
//                if ($warehouse) {
//                    $shipping=$warehouse->shipping;
//                    if ($shipping)
//                        return response(['data' => $shipping, 'message' => 'ok'], 200);
//                    else
//                        return response(['message'=>'not exit shipping in warehouse'],404);
//                } else
//                return response(['message'=>'the warehouse not found'],404);
//        }catch (Exception $ex){
//            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
//        }
//    }
//warehouse   assignOrderToShipping
    public function  assignOrderToShipping($orderId,Request $request)
    {
        try {
            $order = Order::find($orderId);
            if( $order->currentStatus=="warehouse2") {
            if( $order->pickerStatus="complete"){
            if ($order) {
                if ($order->shippingId == null) {
                    $order->shippingId = $request->shippingId;
                    $order
                        ->shipping
                        ->where('id',$request->shippingId)
                        ->increment('countOrders');
                    $order->save();
                    $order = Order::find($orderId);
                    $order->shipping;
                    return response()->json(['data' => $order], 200);
                } else
                {
                    return response()->json(['messege' => 'order assign to shipping already'], 200);
                }
                    return response()->json(['messege' => 'order assign to shipping already'], 200);
            } else
            {
                return response("order not found", 404);}
            }
            else
                return response("assign picker to order first ", 404);
        } else {
        return response(["message" => "dont have permission to do it "], 404);
    }}
        catch (Exception $ex) {
            return response(['data' => $ex->getMessage(), 'message' => 'error'], 400);
        }
    }


    public  function shippingOrdersComplete(){
            try {
                $shipping=auth()->guard('shipping-api')->user();
                if($shipping){
                    $dateNow=now()->format('y-m-d');
                    $orders=$shipping->order;
                    $filteredOrders = [];
                    foreach ($orders as $order) {
                        $dateUpdated = $order->updated_at->format('y-m-d');
                        $diff = strtotime($dateNow) - strtotime($dateUpdated);
                        $days = floor($diff / (60 * 60 * 24));

                        if ($days == 1 && $order->shippingStatus == 'complete') {
                            $filteredOrders[] = $order;
                        }
                    }
                    return response(['data' => $filteredOrders, 'message' => 'ok'], 200);
                } else
                    return response(['message'=>'not shipping found'],404);
            }catch (Exception $ex){
                return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
            }

        }

    public  function shippingOrdersNotComplete(){
        try {
            $shipping=auth()->guard('shipping-api')->user();
            if($shipping){
                $orders=$shipping->order
                    ->where('currentStatus','!=','complete');

                $orders->load('customer:id,latitude,longitude');
                return response(['data'=>$orders,'message'=>'ok'],200);
            } else
                return response(['message'=>'not shipping found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

//shipping
    public function  start($orderId){
        try {
            $order=Order::find($orderId);
            if( $order->currentStatus=="shipping") {
            $order->shippingStatus='delivering';
            $order->save();
            if ($order) {
                return response()->json(['data'=>$order],200);
            } else
                return response("order not found",404);
        } else {
return response(["message" => "dont have permission to do it "], 404);
}}
            catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//shipping
    public function  End($orderId){
        try {
            $order=Order::find($orderId);
            if( $order->currentStatus=="shipping") {
            $order->shippingStatus='complete';
                $countOrders = $order
                    ->shipping
                    ->select('countOrders')
                    ->value('countOrders');
                if ($countOrders > 0) {
            $order
                ->shipping
                ->where('id',$order->shippingId)
                ->decrement('countOrders');
            $order->save();
            if ($order) {
                $order=Order::find($orderId);
                $order->shipping;
                return response()->json(['data'=>$order],200);
            } else
                return response("order not found",404);}
        } else {
                return response(["message" => "dont have permission to do it "], 404);
            }}
        catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function storeMap(Request $request){
        try {
            $user=auth()->guard('shipping-api')->user();
            $map=new Truck();
            $map->latitude=$request->latitude;
            $map->longitude=$request->longitude;
            $map->shippingId=$user->id;
            $map->save();
            if ($map) {
                return response(["message" => "ok"], 201);
            }else
                return response(["message" => "error in validation"], 404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

//    public function showAllLocation($id)
//    {
//        $customer=Customer::find($id);
//        if ($customer) {
//            $location = Map::where('customerId', $id)->first();
//            if ($location)
//                return response()->json(['message' => 'ok', 'data' => $location], 200);
//            else
//                return response()->json(['message' => 'no location for this customer '], 404);
//        }else
//            return response()->json(['message' => 'no customer found '], 404);
//    }

    public function showAllLocation($id)
    {
        $order=Order::find($id);
        $map= $order->customer->map;
        if ($map) {
            return response()->json(['message' => 'ok', 'data' => $map], 200);
        }else
            return response()->json(['message' => 'no location found '], 404);
    }


}
