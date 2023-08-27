<?php

namespace App\Http\Controllers;
use App\Http\Requests\employeeRequest;
use App\Models;
use App\Models\Employee;
use App\Traits\AuthorizedCheckTrait;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class employeeController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth:employee-api', ['except' => ['Register','Login']]);
//    }
    use AuthorizedCheckTrait;
    public  function showAllEmployee(){
        $this->authorizCheck('showAllEmployee');
        $employee= Employee::get();
        return response()->json(['message'=>'ok','data'=>$employee],200);
    }


    public  function add(employeeRequest $request){
        $this->authorizCheck('employeeAdd');
        try {
            $employee=new Employee();
            $employee=$this->processAdd($employee,$request);
            if ($employee) {
                return response(["message"=>"ok",'data'=>$employee],201);
            } else
                return response(["message"=>"error in validation"],404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


    protected function processAdd(Employee $employee,employeeRequest $request){
        $employee->firstName=$request->firstName;
        $employee->middleName=$request->middleName;
        $employee->lastName=$request->lastName;
        $employee->email=$request->email;
        $employee->password=Hash::make($request->password);
        $employee->phoneNum=$request->phoneNum;
        $employee->role=$request->role;
        // $picker->accoutStatus=$request->accoutStatus;
        // $customer->warehouseId=$request->warehouseId;
        $employee->save();
        $employee->assignRole($request->role);
        return $employee;
    }
    protected function process(Employee $employee,Request $request){
        $employee->firstName=$request->firstName;
        $employee->middleName=$request->middleName;
        $employee->lastName=$request->lastName;
        $employee->email=$request->email;
        $employee->password=Hash::make($request->password);
        $employee->phoneNum=$request->phoneNum;
        $employee->role=$request->role;
         // $picker->accoutStatus=$request->accoutStatus;
       // $customer->warehouseId=$request->warehouseId;
        $employee->save();
        return $employee;
    }


    public  function update(Request $request,$id){
        $this->authorizCheck('employeeUpdate');
        try {

            $employee=Employee::findOrFail($id);
            $employee = $this->process($employee, $request);
            if ($employee) {
                return response(['data'=>$employee,'message'=>'ok'],200);

            } else
                return response(['message'=>'not found'],404);
        }catch (\Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }


    }
    public function delete($id){
        $this->authorizCheck('employeeDelete');
        try {
            $employee=Employee::find($id);

            if ($employee) {
                $employee->delete();
                return Response(["message"=>"ok"],200);
            } else
                return response("employee not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }


    }

    public function search($fullName,$phoneNum){
      $this->authorizCheck('employeeSearch');
        try{
            $employee=Employee::where(['firstName'=>$fullName,'phoneNum'=>$phoneNum])->first();
            if ($employee) {

                return Response(["message"=>"ok",'data'=>$employee],200);
            } else
                return response("employee not found",404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }
//    public function showAllUNAcceptCustomer()
//    {
//        try {
//
//            $customer=Customer::where(['status'=>null])->get();
//            if ($customer) {
//                return response(["message"=>"ok",'data'=>$customer],201);
//            } else
//                return response(["message"=>"customer are not found"],201);
//        }catch (Exception $ex){
//
//            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
//        }
//    }



    public function view($id){
        $this->authorizCheck('employeeView');
        try {
            $employee=Employee::find($id);
            if ($employee) {

                return response()->json(['data'=>$employee],200);
            } else
                return response("employee not found",404);
        }catch (Exception $ex){
            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }

    public  function accountAvailable(Request $request ,int $id){
        $this->authorizCheck('employeeAccountAvailable');
        try {
            $validated = $request->validate([
                'accountStatus' => 'required|boolean',
            ]);
            $employee=Employee::find($id);
            $employee->accountStatus=$request->accountStatus;
            $employee->save();
            if ($employee) {
                return response(["message"=>"ok",'data'=>$employee],201);
            } else
                return response(["message"=>"error in validation"],404);
        }catch (Exception $ex){

            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
        }
    }


//    public  function accept(customerAcceptRequest  $request ,int $id){
//
//        try {
//
//            $customer=Customer::find($id);
//            $customer->paymentMethod=$request->paymentMethod;
//            $customer->status=$request->status;
//            $customer->date=$request->date;
//            $customer->warehouseId=$request->warehouseId;
//            $customer->save();
//            if ($customer) {
//                return response(["message"=>"ok",'data'=>$customer],201);
//            } else
//                return response(["message"=>"error in validation"],404);
//        }catch (Exception $ex){
//
//            return response(['data'=>$ex->getMessage(),'message'=>'error'],400);
//        }
//    }

    public function Login()
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth()->guard('employee-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user=auth()->guard('employee-api')->user();
        return Response(['data'=>$user,'token'=>$token]);
    }

    public function me()
    {
//        $this->authorizCheck('employeeProfile');
        $user=auth()->guard('employee-api')->user();
        if ($user)
            return response()->json($user);
        return response()->json(['message'=>'not employee']);
    }


    public function Logout()
    {
        auth()->guard('employee-api')->logout();
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


}
