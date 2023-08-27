<?php

namespace App\Http\Controllers;

use App\Http\Requests\orderRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Picker;
use App\Models\Product;
use App\Traits\AuthorizedCheckTrait;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

class orderController extends Controller
{
    use AuthorizedCheckTrait;
//    admin
    public function showAll()
    {
        $this->authorizCheck('showAllOrder');
        $orders= Order::get();
        foreach ($orders as $order){
           $order
               ->customer
               ->select('customer.firstName','customer.lastName','customer.paymentMethod')
               ->value('customer.firstName','customer.lastName','customer.paymentMethod');
        }

        return response()->json(['message'=>'ok','data'=>$orders],200);
    }

//    all employee
    public function showAllbyRole()
    {
        try {
        $user=Auth::guard('employee-api')->user();
            if(Auth::check()) {
            if ($user->role == "Admin") {
                $orders = Order::get();
                foreach ($orders as $order){
                    $order
                        ->customer
                        ->select('customer.firstName','customer.lastName','customer.paymentMethod')
                        ->value('customer.firstName','customer.lastName','customer.paymentMethod');
                }
                return response()->json(['message'=>'ok','data'=>$orders],200);
            }

            elseif ($user->role == "Credit") {
                $orders = Order::where('currentStatus', 'credit')->get();
                foreach ($orders as $order){
                    $order
                        ->customer
                        ->select('customer.firstName','customer.lastName','customer.paymentMethod')
                        ->value('customer.firstName','customer.lastName','customer.paymentMethod');
                }
                return response()->json(['message'=>'ok','data'=>$orders],200);
            }

            elseif ($user->role == "Sale Manager") {
                $orders = Order::where('currentStatus', 'salesManager')->get();
                foreach ($orders as $order){
                    $order
                        ->customer
                        ->select('customer.firstName','customer.lastName','customer.paymentMethod')
                        ->value('customer.firstName','customer.lastName','customer.paymentMethod');
                }
                return response()->json(['message'=>'ok','data'=>$orders],200);
            }

            elseif ($user->role == "Warehouse Manager") {
                $orders = Order::where('currentStatus', 'warehouse1')
                    ->orWhere('currentStatus', 'warehouse2')
                    ->get();
                foreach ($orders as $order){
                    $order
                        ->customer
                        ->select('customer.firstName','customer.lastName','customer.paymentMethod')
                        ->value('customer.firstName','customer.lastName','customer.paymentMethod');
                }
                return response()->json(['message'=>'ok','data'=>$orders],200);

            }}
        else
            return response()->json(['message'=>'error in auth'],200);
        }catch (Exception $ex)
        {
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

//    public function showOrdersForCridet()
//    {
////        $this->authorizCheck('showAllOrder');
//        $orders= Order::where('currentStatus','credit')->get();
//        foreach ($orders as $order)
//        {
//            $order
//                ->customer
//                ->select('customer.firstName','customer.lastName','customer.paymentMethod')
//                ->value('customer.firstName','customer.lastName','customer.paymentMethod');
//        }
//        return response()->json(['message'=>'ok','data'=>$orders],200);
//    }
//
//    public function showOrdersForSales()
//    {
////        $this->authorizCheck('showAllOrder');
//        $orders= Order::where('currentStatus','salesManager')->get();
//
//        foreach ($orders as $order){
//            $order
//                ->customer
//                ->select('customer.firstName','customer.lastName','customer.paymentMethod')
//                ->value('customer.firstName','customer.lastName','customer.paymentMethod');
//        }
//
//        return response()->json(['message'=>'ok','data'=>$orders],200);
//    }
//
//    public function showOrdersForwarehouse()
//    {
////        $this->authorizCheck('showAllOrder');
//        $orders = Order::where('currentStatus', 'warehouse1')
//            ->orWhere('currentStatus', 'warehouse2')
//            ->get();
//
//        foreach ($orders as $order){
//            $order
//                ->customer
//                ->select('customer.firstName','customer.lastName','customer.paymentMethod')
//                ->value('customer.firstName','customer.lastName','customer.paymentMethod');
//        }
//
//        return response()->json(['message'=>'ok','data'=>$orders],200);
//    }

    public function view($id){
      $this->authorizCheck('viewOrder');
        try {
            $orders=Order::find($id);
            $orders->warehouse;
            $orders->customer;
            $ord=$orders->product;
            $productData=[];
       foreach ($ord as $or)
          {
                  $warehouseName =  $orders->warehouse->name;
                  $productData[] = [
        'namperoduct'=>$or->name,
        'image'=>$or->image,
        'idproduct'=>$or->id,
        'namefirst' => $orders->customer->firstName,
        'nameLast'=>$orders->customer->lastName,
        'orderCreated'=>$orders->created_at,
        'warehouseName'=>$warehouseName,
        'pymentMethods'=>$orders->customer->paymentMethod,
        'email'=>$orders->customer->email,
        'phoneNumber' => $orders->customer->phoneNum,
    ];
        }
            if ($orders)
            {
                return response()->json(['data'=>$productData],200);
            } else
                return response("order not found",404);
        }catch (Exception $ex)
        {
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//customer
    public  function add(Request $request){

        try {
            $order=new Order();
            $customer=Auth::user();
            $order->warehouseId=$customer->warehouseId;
            $order->customerId=$customer->id;
            $order->currentStatus="credit";
            $order->code=Str::random(5);
            $order->save();
            $order=$this->process($order,$request);
            if ($order) {
                return response(["message"=>"ok",'data'=>$order],201);
            } else
                return response(["message"=>"error in add product"],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//    salesManager
    public  function addItem($orderId,Request $request){
       $this->authorizCheck('editOrder');
        try {
            $order=Order::findOrFail($orderId);
            if( $order->currentStatus=="salesManager") {
                $product = Product::find($request->productId);
                $pric = $product->price;
                if ($order) {
                    if (!$order
                        ->product()
                        ->where('productId', $request->productId)
                        ->exists()) {
                        $order->product()->attach($product, [
                            'orderedQuantity' => $request->orderedQuantity,
                            'approvedQuantity' => $request->orderedQuantity,
                            'pricePeices' => $request->orderedQuantity * $pric,
                        ]);
                        $order->approvedQuantity += $request->orderedQuantity;
                        $order->totalAmount += ($request->orderedQuantity * $pric);
                        $product->increment('trending');
                        $order->save();
                        return response(["message" => "ok", 'data' => $order], 201);
                    } else
                        return response(["message" => "order contain this product"], 201);

                } else
                    return response(["message" => "error in add product"], 404);
            }else
            {
                return response(["message" => "dont have permission to do it "], 404);
            }
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


//    public  function update($id,orderRequest $request){
//        try {
//            $order=Order::findOrFail($id);
//            $order = $this->process($order, $request);
//            if ($order) {
//                return response(['data'=>$order,'message'=>'ok'],200);
//            } else
//                return response(['message'=>'the order not found'],404);
//        }catch (Exception $ex){
//            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
//        }
//    }
//saleManager
    public function delete($id){
        $this->authorizCheck('orderDelete');
        try {
            $order=Order::find($id);
            if( $order->currentStatus=="salesManager") {
            if ($order) {
                $order->product()->detach();
                $order->delete();
                return Response(["message"=>"ok"],200);
            } else
                return response("order not found",404);
        }
            else
            {
                return response(["message" => "dont have permission to do it "], 404);
            }
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//    salesManager
    public function deleteItem($orderId,$itemId){
        $this->authorizCheck('editOrder');
        try {
            $order=Order::find($orderId);
            if( $order->currentStatus=="salesManager") {
                if ($order) {
                    if ($order
                        ->product()
                        ->where('productId', $itemId)
                        ->exists()) {
                        $approve = $order
                            ->product()
                            ->where('productId', $itemId)
                            ->select('approvedQuantity')
                            ->value('approvedQuantity');

                        $product = Product::find($itemId);

                        $order->product()->detach($product);

                        $order->approvedQuantity -= $approve;
                        $order->totalAmount -= ($product->price * $approve);
                        $order->save();

                        return Response(["message" => "ok"], 200);
                    } else
                        return response("order not contain in this product", 404);
                } else
                    return response("order not found", 404);
            }
          else  {
                return response(["message" => "dont have permission to do it "], 404);
            }
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    protected function process( Order $order,Request $request){
        $products = $request->input('products');
        foreach ($products as $item){
            $p=Product::find($item['productId']);
            $pric=$p->price;
            $order->product()->attach($item['productId'],[
                'orderedQuantity'=>$item['orderedQuantity'],
                'approvedQuantity'=>$item['orderedQuantity'],
                'pricePeices'=>$item['orderedQuantity']*$pric,
            ]);
            $order->totalAmount=$request->totalAmount;
            $p->increment('trending');
            $order->approvedQuantity+= $item['orderedQuantity'];
            $order->save();
        }
        return $order;
    }

//saleManager
//    public function  productMessage($orderId,$productId, Request $request){
//        try {
//            $order=Order::find($orderId);
//            if( $order->currentStatus=="salesManager") {
//                $product = Product::find($productId);
//                $order->product()->updateExistingPivot($product, [
//                    'message' => $request->message,
//                ]);
//                $order->product;
//                if ($order) {
//
//                    return response()->json(['data' => $order], 200);
//                } else
//                    return response("order not found", 404);
//            }
//            else {
//                return response(["message" => "dont have permission to do it "], 404);
//            }
//        }catch (Exception $ex){
//            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
//        }
//    }

    public function  productMessage($orderId,$productId, Request $request){
        try {
            $order=Order::find($orderId);
            if( $order->currentStatus=="salesManager") {
                $product = Product::find($productId);
                $order->product()->updateExistingPivot($product, [
                    'message' => $request->message,
                ]);
                $order->product;
                if ($order) {

                    return response()->json(['data' => $order], 200);
                } else
                    return response("order not found", 404);
            }
            else {
                return response(["message" => "dont have permission to do it "], 404);
            }
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function  checkProduct($orderId){
        try {
            $order=Order::find($orderId);

            $order->checkProduct=1;
            $order->save();
            if ($order) {

                return response()->json(['data'=>$order],200);
            } else
                return response("order not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

//salesManager
    public function  decrementItem($orderId,$itemId){
       $this->authorizCheck('editOrder');
        try {
            $order=Order::find($orderId);
            if( $order->currentStatus=="salesManager") {
            $product=Product::find($itemId);
            $pric=$product->price;
            if ($order) {
                if($order->product()
                    ->where('productId', $itemId)
                    ->exists())
                {
                    $approvedQuantity=$order->product()
                        ->wherePivot('productId', $itemId)
                        ->select('approvedQuantity')
                        ->value('approvedQuantity');


                    if($approvedQuantity>0){
                        $approvedQuantity=$order->product()
                            ->where('productId', $itemId)->decrement('approvedQuantity');

                        $approvedQuantity=$order->product()
                            ->wherePivot('productId', $itemId)
                            ->select('approvedQuantity')
                            ->value('approvedQuantity');
                        $order->product()->updateExistingPivot($itemId,[
                            'pricePeices'=>$approvedQuantity*$pric
                        ]);

                        $order->decrement('approvedQuantity');
                        $order->totalAmount-=$pric;
                    }
                    $item=$order->product()
                        ->where('productId', $itemId)->get();

                    return response(["date"=>$item],200);

                }
                else
                {
                    return response()->json(['messege'=>'product not found in this order'],200);
                }


            } else
                return response("order not found",404);
        }else {
                return response(["message" => "dont have permission to do it "], 404);
            }}catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function  incrementItem($orderId,$itemId){
       $this->authorizCheck('editOrder');
        try {
            $order=Order::find($orderId);
            if( $order->currentStatus=="salesManager") {
            $product=Product::find($itemId);
            $pric=$product->price;
            if ($order) {
                    if($order
                    ->product()
                    ->where('productId', $itemId)
                    ->exists())
                {
                    $approvedQuantity=$order->product()
                        ->wherePivot('productId', $itemId)
                        ->select('approvedQuantity')
                        ->value('approvedQuantity');

                        $orderedQuantity=$order->product()
                        ->wherePivot('productId', $itemId)
                        ->select('orderedQuantity')
                        ->value('orderedQuantity');

                          $p=Product::find($itemId);
                        $q=$p->warehouse()
                        ->select('stock.quantity')
                        ->value('stock.quantity');

                    if($approvedQuantity < $orderedQuantity &&$approvedQuantity<$q ){
                    $order->product()
                        ->where('productId', $itemId)->increment('approvedQuantity');

                        $approvedQuantity=$order->product()
                            ->wherePivot('productId', $itemId)
                            ->select('approvedQuantity')
                            ->value('approvedQuantity');
                        $order->product()->updateExistingPivot($itemId,[
                            'pricePeices'=>$approvedQuantity*$pric
                        ]);

                    $order->increment('approvedQuantity');
                        $order->totalAmount+=$pric;

                    $item=$order->product()
                          ->where('productId', $itemId)->get();
                    return response(["date"=>$item],200);
                }
                   }

                else
                {
                    return response()->json(['messege'=>'product not found in this order'],200);
                }


            } else
                return response("order not found",404);
        }else {
            return response(["message" => "dont have permission to do it "], 404);
        }
        }
        catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function  orderMessage($orderId){
        try {
        $customer=auth()->guard('customer-api')->user();
        if($customer){
            $order=Order::find($orderId);
            $messages[]=null;
            if ($order) {
//                if($order->currentStatus=='salesManager'&& $order->checkProduct==0){
//                    return response()->json(['data'=>"this order is in prosssecing statuse"],200);
//                }
//                if ($order->currentStatus=='salesManager'&& $order->checkProduct==1){

                    $items=$order->product;
                    foreach($items as $item) {
                        $product = Product::find($item->id);
                        if ($order->product()
                            ->where('productId', $product->id)
                            ->wherePivot('message', '!=', null)
                            ->exists())
                            $messages = $item;

                    }
                    if(!$messages){
                        return response()->json(['message' => 'approve'], 200);
                    }
                        return response()->json(['data' => $messages], 200);
//                    }
//                else
//                    return response()->json(['orderStatuse'=>$order->currentStatus,"data"=>$order],200);
            }
 else
                return response("order not found",404);
     }
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

//salesManager
    public function edit($id){
        try {
//            $this->authorizCheck('editOrder');
            $orders=Order::find($id);
            if( $orders->currentStatus=="salesManager"|| $orders->currentStatus=="picked") {
                $products = $orders->product;
                foreach ($products as $product) {

                    $product
                        ->load(['category:id,nameCategory', 'warehouse' => function ($query) use ($orders) {
                            $query
                                ->where('warehouseId', $orders->warehouseId)
                                ->select('name', 'stock.quantity');
                        }]);
                }
                if ($products) {
                    return response()->json(['message' => 'ok', 'data' => $products], 200);
                } else
                    return response("order not found", 404);
            }
            else {
                return response(["message" => "dont have permission to do it "], 404);
            }
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


                //salesManager
                public function editpicker($id){
                    try {
//            $this->authorizCheck('editOrder');
                        $orders=Order::find($id);
                        if(  $orders->currentStatus=="picked") {
                            $products = $orders->product;



                            ;

//                            $orders
//                                ->product()
//                                ->where('productId', $productId)
//                                ->select('pickedQuantity')
//                                ->value('pickedQuantity')
//                            foreach ($products as $product) {
//
//                                $product
//                                    ->load(['category:id,nameCategory', 'warehouse' => function ($query) use ($orders) {
//                                        $query
//                                            ->where('warehouseId', $orders->warehouseId)
//                                            ->select('name', 'stock.quantity');
//                                    }]);
//                            }
//                $categoryName = $product->category ? $product->category->nameCategory : '';
//               $warehouses= $product->warehouse;
//                foreach ($warehouses as $warehouse){
//                    $warehouseName = $warehouse->name;
//                    $quantity = $warehouse->pivot->quantity;
//                }
//                $productData[] = [
//                    'name' => $product->name,
//                    'weight'=>$product->weight,
//                    'image'=>$product->image,
//                    'price'=>$product->price,
//                    'available'=>$product->available,
//                    'categoryName' => $categoryName,
//                    'warehouseName' => $warehouseName,
//                    'quantity' => $quantity
//                ];

                if ($products) {
                    return response()->json(['message' => 'ok', 'data' => $products], 200);
                } else
                    return response("order not found", 404);
            }
            else {
                return response(["message" => "dont have permission to do it "], 404);
            }
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function search($name){
        try{
            $order=Order::where(['name'=>$name])->first();
            if ($order) {

                return Response(["message"=>"ok",'data'=>$order],200);
            } else
                return response("product not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function  reject(Request $request,$orderId){
      $this->authorizCheck('rejectOrder');
        try {
            $user=auth()->guard('employee-api')->user();
            $order=Order::find($orderId);
            if(( $order->currentStatus=="salesManager"&& $user->role=="Sale Manager" )
                ||($order->currentStatus=="credit"&& $user->role=="Credit")
                ||($order->currentStatus=="warehouse1" && $user->role=="Warehouse Manager"  )
                ||($order->currentStatus=="warehouse2" && $user->role=="Warehouse Manager"  )) {
            $order->reason=$request->reason;
            $order->currentStatus="reject";
            $order->save();
            if ($order) {
                return response()->json(['data'=>$order],200);
            } else
                return response("order not found",404);
        } else {
                return response(["message" => "dont have permission to do it "], 404);
            }

        } catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

########################################################################
//warehouse
    public  function pickerOrders(){
        try {
            $this->authorizCheck('pickerOrders');
            $orders = Order::whereHas('picker')
                ->select('id','totalAmount','customerId','pickerId','progress','totalCase','pickerStatus','pickedQuantity','approvedQuantity')->get();
            if ($orders) {
                foreach ($orders as $order) {
                    $order->load(['picker:id,firstName,lastName', 'customer:id,firstName,lastName'
                    ]);
                }
                return response(['data'=>$orders,'message'=>'ok'],200);
            } else
                return response(['message'=>'the order not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//picker
    public function  start($orderId){
        try {
            $order=Order::find($orderId);
            if( $order->currentStatus=="picked") {
            $order->pickerStatus='Processing';
            $order->save();
            if ($order) {
                return response()->json(['data'=>$order],200);
            } else
                return response("order not found",404);
        }

            else {
                return response(["message" => "dont have permission to do it "], 404);
            }

        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//picker
    public function  End($orderId){
        try {
            $order = Order::find($orderId);
            if ($order->currentStatus == "picked") {
                $order->currentStatus="warehouse2";
                $order->pickerStatus = 'complete';

                $countOrders = $order
                    ->picker
                    ->select('countOrders')
                    ->value('countOrders');
                if ($countOrders > 0) {
                    $order
                        ->picker
                        ->where('id', $order->pickerId)
                        ->decrement('countOrders');
                    $order->save();
                    if ($order) {
                        $order = Order::find($orderId);
                        $order->picker;
                        return response()->json(['data' => $order], 200);
                    } else
                        return response("order not found", 404);
                }
            }

            else {
                return response(["message" => "dont have permission to do it "], 404);
            }
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

//picker
    public function  scan($orderId,$productId)
    {
        try {
            $order = Order::findOrFail($orderId);
            if( $order->currentStatus=="picked") {
//            $product=Product::find($productId);
                $order->product;
            if ($order) {
                if ($order
                    ->product()
                    ->where('productId', $productId)
                    ->exists()) {
                    $approvedQuantityb = $order
                        ->product()
                        ->where('productId', $productId)
                        ->select('approvedQuantity')
                        ->value('approvedQuantity');

                    $pickedQuantityb = $order
                        ->product()
                        ->where('productId', $productId)
                        ->select('pickedQuantity')
                        ->value('pickedQuantity');

                    $p = Product::find($productId);
                    $quantity = $p->warehouse()
                        ->select('stock.quantity')
                        ->value('stock.quantity');

                    if ($pickedQuantityb < $approvedQuantityb && $approvedQuantityb < $quantity) {
                        $order->product()
                            ->where('productId', $productId)
                            ->increment('pickedQuantity');
                        $order->save();

                        $order
                            ->where('id', $orderId)
                            ->increment('pickedQuantity');
                        $order->save();

                        $approvedQuantity = $order
                            ->where('id', $orderId)
                            ->select('approvedQuantity')
                            ->value('approvedQuantity');

                        $pickedQuantity = $order
                            ->where('id', $orderId)
                            ->select('pickedQuantity')
                            ->value('pickedQuantity');

                        $totalCase = $pickedQuantity .'/'. $approvedQuantity;
                        $progress = ($pickedQuantity /  $approvedQuantity) * 100;

                        $order->totalCase = $totalCase;
                        $order->progress = $progress;
                        $order->save();
                        $order = Order::findOrFail($orderId);
                        $order->product;
                        return response()->json(['data' => $order], 200);
                    }  else{
                        return response()->json(['messege'=>'can not scan this product more because approvedQuantity has completed'],200);
                    }
                } else {
                    return response()->json(['messege' => 'product not found in this order'], 200);
                }
            } else
                return response("order not found", 404);
        }
            else {
        return response(["message" => "dont have permission to do it "], 404);
    }
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function confirm(Request $request){
        try {
            $employee=auth()->guard('employee-api')->user();
            $role=$employee->role;
            $orderId=$request->orderId;
            $order=Order::find($orderId);
            if ($role) {
                if($order->currentStatus=='credit' &&($role=="Credit Manager" || $role=="Admin"))
                {
                    $order->currentStatus="salesManager";
                    $order->save();
                }

                elseif ($order->currentStatus=='salesManager' &&($role=="Sale Manager"|| $role=="Admin"))
                {
                    $order->currentStatus="warehouse";
                    $order->save();
                }

                elseif($order->currentStatus=='warehouse' &&($role=="Warehouse Manager"|| $role=="Admin"))
                {
                    $order->currentStatus="picked";
                    $order->save();
                }
                else {
                    return response(['message'=>'no authorized'],200);
                }

                return response(['data'=>$order,'message'=>'ok'],200);
            } else
                return response(['message'=>'the order not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

//picker
    public  function pickerISOrders(){
        try {
            $picker=auth()->guard('pickers-api')->user();
            if($picker){
                $orders=$picker->order
                    ->where('currentStatus','picked')
                    ->get();
                foreach ($orders as $order){
                    $order->load(['customer:firstName,lastName,id']);
                }
                return response(['data'=>$picker,'message'=>'ok'],200);
            } else
                return response(['message'=>'the order not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


//picker
    public  function pickerOrdersComplete(){
        try {
            $picker=auth()->guard('pickers-api')->user();
            if($picker){
               // $dateNow=now()->format('y-m-d');
                $orders=$picker->order;
                $filteredOrders = [];
                foreach ($orders as $order) {
//                    $filteredOrders[]=$order->where('pickerStatus','complete');
//                    $dateUpdated = $order->updated_at->format('y-m-d');
//                    $diff = strtotime($dateNow) - strtotime($dateUpdated);
//                    $days = floor($diff / (60 * 60 * 24));

                    if ( $order->pickerStatus == 'complete') {
                        $filteredOrders[] = $order;
                    }
                }
                return response(['data' => $filteredOrders, 'message' => 'ok'], 200);
            } else
                return response(['message'=>'not picker found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//picker
    public  function pickerOrdersNotComplete(){
        try {
            $picker=auth()->guard('pickers-api')->user();
            if($picker){
                $orders=$picker->order;
                $filteredOrders = [];
                foreach ($orders as $order) {
//                    $filteredOrders[]=$order->where('pickerStatus','!=','complete');
                    if ( $order->pickerStatus != 'complete') {
                        $filteredOrders[] = $order;
                    }
                }
                return response(['data' => $filteredOrders, 'message' => 'ok'], 200);
            } else
                return response(['message'=>'not picker found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//warehouse
    public function  assignOrderToPicker($orderId,Request $request)
    {
        try {
            $this->authorizCheck('assignOrderToPicker');
            $order = Order::find($orderId);
            if( $order->currentStatus=="warehouse1") {
            if ($order) {
                if ($order->pickerId == null) {
                    $order->pickerId = $request->pickerId;
                    $order->dateAssignIt = now()->format('y-m-d');
                    $order->save();
                    $order
                        ->picker
                        ->where('id', $request->pickerId)
                        ->increment('countOrders');

//                $order->save();
                    $order = Order::find($orderId);
                    $order->picker;
                    return response()->json(['data' => $order], 200);
                } else
                    return response()->json(['messege' => 'order assign to picker already'], 200);
            } else
                return response("order not found", 404);
        }
              else {
        return response(["message" => "dont have permission to do it "], 404);
    }
        }
        catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public  function confirmCredit($orderId){
       $this->authorizCheck('confirmCredit');
        try {
            $order=Order::find($orderId);
            if ($order) {
                if($order->currentStatus=="credit")
                {
                    $order->currentStatus="salesManager";
                    $order->save();
                }

                return response(['data'=>$order,'message'=>'ok'],200);
            } else
                return response(['message'=>'the order not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function confirmSalesManager($orderId){
       $this->authorizCheck('confirmSalesManager');
        try {
            $order=Order::find($orderId);

            if ($order) {
                if ($order->currentStatus=="salesManager")
                {
                    $order->currentStatus="warehouse1";
                    $order->save();
                }

                return response(['data'=>$order,'message'=>'ok'],200);
            } else
                return response(['message'=>'the order not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function confirmWarehouse($orderId){
      $this->authorizCheck('confirmWarehouse');

        try {
            $order=Order::find($orderId);

            if ($order) {

                if($order->currentStatus=="warehouse1")
                {
                    if ($order->pickerId != null) {
                        $order->currentStatus = "picked";
                        $order->save();
                        return response(['data'=>$order,'message'=>'ok'],200);
                    }
                    else{
                        return response(['message'=>'must to assign picker to order first'],200);
                    }
                }
                elseif($order->currentStatus=="warehouse2")
                {
                    if ($order->shippingId != null) {
                        $order->currentStatus = "shipping";
                        $order->save();
                        return response(['data'=>$order,'message'=>'ok'],200);
                    }else {
                        return response(['message' => 'must to assign shipping to order first  '], 200);
                    }

            } else
                return response(['message'=>'the order not found'],404);
        }}catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function confirmPicked($orderId){
       $this->authorizCheck('confirmPicked');

        try {
            $order=Order::find($orderId);

            if ($order) {

                if($order->currentStatus=="picked")
                {
                    $order->currentStatus="warehouse2";

                    $order->save();
                    $invoice=new Invoice();
                    $invoice->orderId=$orderId;
                    $invoice->save();
                }

                return response(['data'=>$order,'message'=>'ok'],200);
            } else
                return response(['message'=>'the order not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

//shipping
    public  function confirmShipping($orderId){
        try {
            $order=Order::find($orderId);

            if( $order->pickerStatus="complete")
            {
                $order->currentStatus="complete";
                $order->save();

                return response(['data'=>$order,'message'=>'ok'],200);
            } else
                return response(['message'=>'the order not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//shipping
    public  function shippingISOrders(){
        try {
            $shipping=auth()->guard('shipping-api')->user();
            if($shipping){
                $orders=$shipping->order
                    ->where('currentStatus','shipping')
                    ->get();
                foreach ($orders as $order){
                    $order->load(['customer:firstName,lastName,id']);
                }
                return response(['data'=>$shipping,'message'=>'ok'],200);
            } else
                return response(['message'=>'the order not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function viewPickedQuantity($orderId,$productId){
        try {
            $orders=Order::find($orderId);
            if ($orders) {
                $products = $orders
                    ->product;
                foreach ($products as $product) {
                    $pro=  $orders
                        ->product()
                        ->where('productId', $productId)
                      ->select('pickedQuantity')
                      ->value('pickedQuantity')
//                        ->get()
                    ;
                    if ($product)
                        return response(['data' => $pro, 'message' => 'ok'], 200);

                }
            }
            else
                return response(['message' => 'not order found'], 404);
        }
        catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

//    public function generateBarcode($id)
//    {
//        $product=Product::find($id);
//        $barcode = new DNS1D();
//        $barcode->setStorPath(public_path('barcodes')); // Set the storage path for generated barcodes
//        $code =  Str::random(40);
//        $imageData = $barcode->getBarcodePNG($code, 'C39+'); // Generate PNG barcode and store the image data
//
//        if ($imageData !== false)
//        {
//            // Image generation successful
//            file_put_contents(public_path('barcodes/'.$code.'.png'), $imageData);
//            $product->code=$code;
//            $product->imageBarcode=$imageData.'.png';
//            $product->save();
//            return response()->file(public_path('barcodes/'.$code.'.png'));
//        }
//        else {
//            // Image generation failed
//            return response(['message' => 'Barcode image generation failed!'], 404);
//        }
//    }
//picker
    public  function storeBarcode(Request $request){

        try {
            $product=Product::find($request->id);
            $product->code=$request->code;

            $product->save();
            if ($product) {
                return response(["message"=>"ok",'data'=>$product],201);
            } else
                return response(["message"=>"error in code product"],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//picker
    public  function storeBarcodeforOrder(Request $request){

        try {
            $order=Order::find($request->id);
            $order->code=$request->code;

            $order->save();
            if ($order) {
                return response(["message"=>"ok",'data'=>$order],201);
            } else
                return response(["message"=>"error in code product"],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
################################################################
    //customer
    public  function scanorderbyCustomer(Request $request){
        try {
            $customer=auth()->guard('customer-api')->user();
           $orders= $customer->order;
           foreach ($orders as $order){
               if( $order->code==$request->code){
                   $order->delivering=1;
                   $order->save();
               }
           }
            if ($orders) {
                return response(["message"=>"ok",'data'=>$orders],201);
            } else
                return response(["message"=>"order not found"],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

}
