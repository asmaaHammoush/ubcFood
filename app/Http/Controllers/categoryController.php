<?php

namespace App\Http\Controllers;

use App\Http\Requests\categoryRequest;
use App\Models\Category;
use App\Traits\AuthorizedCheckTrait;
use Exception;
use Illuminate\Http\Request;

class categoryController extends Controller
{
    use AuthorizedCheckTrait;
    public function showAll()
    {
//        $this->authorizCheck('showAllCategory');
        $category= Category::get();
        return response()->json(['message'=>'ok','data'=>$category],200);
    }


    public  function add(categoryRequest $request){
        $this->authorizCheck('categoryAdd');
        try {
            $category=new Category();
            $category=$this->processAdd($category,$request);
            if ($category) {
                return response(["message"=>"ok",'data'=>$category],201);
            } else
                return response(["message"=>"error in add category"],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public  function update($id,categoryRequest $request){
        try {
            $category=Category::findOrFail($id);
            $category = $this->process($category, $request);
            if ($category) {
                return response(['data'=>$category,'message'=>'ok'],200);
            } else
                return response(['message'=>'the category not found'],404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    public function delete($id){
        $this->authorizCheck('categoryDelete');
        try {
            $category=Category::find($id);
            if ($category) {
                $category->delete();
                return Response(["message"=>"ok"],200);
            } else
                return response("category not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    protected function process(Category $category,Request $request){
        $category->nameCategory=$request->nameCategory;
        $category->image=$request->image;
//        $image = $request->image;
//        $imageName = time() . '.' . $image->getClientOriginalExtension();
//        $image->move(public_path('Category'), $imageName);
//        $category->image=$imageName;
        $category->save();
        return $category;
    }

    protected function processAdd(Category $category,categoryRequest $request){
        $category->nameCategory=$request->nameCategory;
        $elements = explode('\\', $request->image);
        $lastElement = end($elements);
        $category->image=$lastElement;
//        $image = $request->image;
//        $imageName = time() . '.' . $image->getClientOriginalExtension();
//        $image->move(public_path('Category'), $imageName);
//        $category->image=$imageName;
        $category->save();
        return $category;
    }

    public function view($id){
        try {
            $category=Category::find($id);
            if ($category) {
                return response()->json(['data'=>$category],200);
            } else
                return response("category not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public function search($nameCategory){
        try{
            $category=Category::where(['nameCategory'=>$nameCategory])->first();
            if ($category) {

                return Response(["message"=>"ok",'data'=>$category],200);
            } else
                return response("category not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }



        public function viewCategoryInProduct($id){
            try {
                $category=Category::find($id);
               $products=$category->product;
                if ($products) {
                    return response()->json(['data'=>$products],200);
                } else
                    return response("category not found",404);
            }catch (Exception $ex){
                return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
            }
        }


    public function viewCategoryInProductForCustomer($id){
        try {
            $category=Category::find($id);
            $products=$category->product
            ->where('available',1);

            if ($products) {
                return response()->json(['data'=>$products],200);
            } else
                return response("category not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
}
