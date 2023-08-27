<?php

namespace App\Http\Controllers;

use App\Http\Requests\categoryRequest;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Order;
use App\Traits\AuthorizedCheckTrait;
use Exception;
use Illuminate\Http\Request;

class invoiceController extends Controller
{

//    public  function create($orderId){
//        try {
//            $invoice=new Invoice();
//          $invoice->orderId=$orderId;
//          $invoice->save();
//            if ($invoice) {
//                return response(["message"=>"ok",'data'=>$invoice],201);
//            } else
//                return response(["message"=>"error in validation"],404);
//        }catch (Exception $ex){
//
//            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
//        }
//    }
    public function viewInvoice($id){
        try {
            $orders=Order::find($id);
            $products = $orders
                ->load(['customer:firstName,lastName,id,address,city,phoneNum,email','invoice:orderId,created_at'])
                ->product;
//            foreach ($products as $product) {
//                $product
//                    ->load(['category:id,nameCategory']);
//            }
            return response(['data'=>$orders,'message'=>'ok'],200);
        }
        catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

}
