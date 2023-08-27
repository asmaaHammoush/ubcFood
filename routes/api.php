<?php

use App\Http\Controllers\categoryController;
use App\Http\Controllers\cuctomerController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\invoiceController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\pickerController;
use App\Http\Controllers\productController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\shippingController;
use App\Http\Controllers\stockController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//route::group([
//    'middleware'=>'Cors',
////    'prefix'=>'invoice'
//],function() {
 ############################### picker ######################
route::post('picker/Login',[pickerController::class, 'pickerLogin'])->name('login');

route::group(
    [
         'middleware'=>'auth.guard:employee-api',
        'prefix'=>'picker'
    ],function() {
route::post('/Register',[pickerController::class, 'pickerRegister']);
route::post('/accountAvailable/{id}',[pickerController::class, 'accountAvailable']);
route::get('/showPickerInWarehouse/{id}',[pickerController::class, 'showPickerInWarehouse']);
route::post('/Update/{id}',[pickerController::class, 'update']);
route::delete('/Delete/{id}',[pickerController::class, 'delete']);
route::get('/Search/{fullName}/{phoneNum}', [pickerController::class, 'search']);
route::get('/showAll',[pickerController::class, 'showAllPicker']);
});

route::group(
    [
      'middleware'=>'auth.guard:pickers-api',
        'prefix'=>'picker'
    ],function() {
    route::get('/show', [pickerController::class, 'me'])->name('show');
    route::post('/Logout',[pickerController::class, 'pickerLogout']);
});


########################## shipping ##############################

route::post('shipping/Login',[shippingController::class, 'Login']);

route::group(
    [
        'middleware'=>'auth.guard:employee-api',
        'prefix'=>'shipping'
    ],function() {
    route::post('Register',[shippingController::class, 'Register']);
    route::post('/Update/{id}',[shippingController::class, 'update']);
    route::delete('/Delete/{id}',[shippingController::class, 'delete']);
    route::get('/Search/{firstName}/{lastName}', [shippingController::class, 'search']);
    route::get('/showAll',[shippingController::class, 'showAllShipping']);
    route::post('/accountAvailable/{id}',[shippingController::class, 'accountAvailable']);
    route::get('/viewShippingInWarehouse/{id}',[shippingController::class, 'viewShippingInWarehouse']);
    route::put('/assignOrderToShipping/{id}',[shippingController::class, 'assignOrderToShipping']);

});

route::group(
    [
        'middleware'=>'auth.guard:shipping-api',
        'prefix'=>'shipping'
    ],function() {
    route::get('/viewInvoice/{id}',[orderController::class, 'viewInvoice']);
    route::post('Logout',[shippingController::class, 'Logout']);
    route::get('show', [shippingController::class, 'me']);
    route::get('/start/{orderId}',[shippingController::class, 'start']);
    route::get('/End/{orderId}',[shippingController::class, 'End']);
    route::post('storeMap',[shippingController::class, 'storeMap']);
    route::get('showAllLocation/{id}',[shippingController::class, 'showAllLocation']);
    route::get('shippingOrdersComplete',[shippingController::class, 'shippingOrdersComplete']);
    route::get('shippingOrdersNotComplete',[shippingController::class, 'shippingOrdersNotComplete']);
});

########################## customer ##############################
route::post('customer/Register',[cuctomerController::class, 'Register']);
route::post('customer/Login',[cuctomerController::class, 'Login'])->name('login');
route::group(
    [
        'middleware'=>'auth.guard:employee-api',
        'prefix'=>'customer'
    ],function() {

route::get('/Search/{fullName}/{phoneNum}', [cuctomerController::class, 'search']);
route::get('/showAll',[cuctomerController::class, 'showAllCustomer']);
route::post('/accountAvailable/{id}',[cuctomerController::class, 'accountAvailable']);
route::get('/showAllUNAccept',[cuctomerController::class, 'showAllUNAcceptCustomer']);
route::get('/showAllAccept',[cuctomerController::class, 'showAllAcceptCustomer']);
route::post('/accept/{id}',[cuctomerController::class, 'accept']);
route::post('/reject/{id}',[cuctomerController::class, 'reject']);
route::post('/Update/{id}',[cuctomerController::class, 'update']);
route::delete('/Delete/{id}',[cuctomerController::class, 'delete']);
//route::post('storeMap',[cuctomerController::class, 'storeMap']);
});

route::group(
    [
        'middleware'=>'auth.guard:customer-api',
        'prefix'=>'customer'
    ],function() {
    route::post('/scanorderbyCustomer',[orderController::class, 'scanorderbyCustomer']);
    route::get('/viewInvoice/{id}',[orderController::class, 'viewInvoice']);
    route::get('showAllLocation/{id}',[cuctomerController::class, 'showAllLocation']);
    route::get('/orderMessage/{id}',[orderController::class, 'orderMessage']);
    route::get('/show', [cuctomerController::class, 'me']);
    route::post('/Logout',[cuctomerController::class, 'Logout']);
    route::get('/orderCustomersNotDone',[cuctomerController::class, 'orderCustomersNotDone']);
    route::get('/orderStatuse/{id}',[cuctomerController::class, 'orderStatuse']);


});



########################## employee ##############################
//route::post('employee/add',[employeeController::class, 'add']);
route::post('employee/login',[employeeController::class, 'Login']);

route::group(
    [
        'middleware'=>'auth.guard:employee-api',
        'prefix'=>'employee'
    ],function() {
    route::post('/add',[employeeController::class, 'add']);
    route::get('/show', [employeeController::class, 'me']);
    route::put('/update/{id}',[employeeController::class, 'update']);
    route::post('/delete/{id}',[employeeController::class, 'delete']);
    route::get('/search/{fullName}/{phoneNum}', [employeeController::class, 'search']);
    route::post('/logout',[employeeController::class, 'Logout']);
    route::get('',[employeeController::class, 'showAllEmployee']);
    route::get('/view/{id}',[employeeController::class, 'view']);
    route::put('/update/accountStatus/{id}',[employeeController::class, 'accountAvailable']);
  //  route::get('/showAllUNAccept',[employeeController::class, 'showAllUNAcceptCustomer']);
 //   route::post('/accept/{id}',[employeeController::class, 'accept']);

});


##################### wareHouse ################################
route::group([
    'middleware'=>'auth.guard:employee-api',
    'prefix'=>'warehouse'
],function() {
    route::post('/add',[warehouseController::class, 'add']);
    route::put('/update/{id}',[warehouseController::class, 'update']);
    route::post('/delete/{id}',[warehouseController::class, 'delete']);
    route::get('/view/{id}',[warehouseController::class, 'view']);
    route::get('',[warehouseController::class, 'showAll']);
    route::get('/search/{name}', [warehouseController::class, 'search']);
  //  route::get('/viewProductWarehouse/{id}', [warehouseController::class, 'viewProductWarehouse']);

});


##################### Category ################################
route::group([
    'middleware'=>'auth.guard:employee-api',
    'prefix'=>'category'],function() {
    route::post('/add',[categoryController::class, 'add']);
    route::put('/update/{id}',[categoryController::class, 'update']);
    route::post('/delete/{id}',[categoryController::class, 'delete']);
    route::get('/search/{name}', [categoryController::class, 'search']);
    route::get('',[categoryController::class, 'showAll']);
    route::get('/viewProductCategory/{id}', [categoryController::class, 'viewCategoryInProduct']);
});


route::group([
    'prefix'=>'guest'],function() {
route::get('/view/{id}',[categoryController::class, 'view']);
route::get('',[categoryController::class, 'showAll']);
route::get('/showProductFinal',[productController::class, 'showProductFinal']);
route::get('/showProductTrending',[productController::class, 'showProductTrending']);
route::get('/viewCategoryInProductForCustomer/{categoryId}',[categoryController::class, 'viewCategoryInProductForCustomer']);


});

##################### product ################################
route::group([
    'middleware'=>'auth.guard:employee-api',
    'prefix'=>'product'
],function() {
    route::get('',[productController::class, 'showAll']);
    route::post('/add',[productController::class, 'add']);
    route::put('/update/{id}',[productController::class, 'update']);
    route::post('/delete/{id}',[productController::class, 'delete']);
    route::get('/view/{id}',[productController::class, 'view']);
    route::get('/search/{name}', [productController::class, 'search']);
    route::post('/availableProduct/{id}', [productController::class, 'availableProduct']);
});

//route::get('product',[productController::class, 'showAll'])->middleware('auth.guard:employee-api');
//route::get('product/showProductFinal',[productController::class, 'showProductFinal'])->middleware('auth.guard:customer-api');
//route::get('product/showProductTrending',[productController::class, 'showProductTrending'])->middleware('auth.guard:customer-api');
route::get('product/viewProductAvailable',[productController::class, 'viewProductAvailable'])->middleware('auth.guard:customer-api');



##################### stock ################################
route::group([
    'middleware'=>'auth.guard:employee-api',
    'prefix'=>'stock'],function() {
    //route::post('/add/{id}',[stockController::class, 'add']);
    route::put('/updateQuantity/{id}',[stockController::class, 'updateQuantity']);
    route::post('/delete/{id}',[stockController::class, 'delete']);
    route::get('/view/{id}',[stockController::class, 'view']);



});

##################### Role ################################
route::group(['prefix'=>'role'],function() {
    route::post('/add',[roleController::class, 'add']);
    route::put('/update/{id}',[roleController::class, 'update']);
    route::post('/delete/{id}',[roleController::class, 'delete']);
    route::get('/view/{id}',[roleController::class, 'view']);
    route::get('',[roleController::class, 'showAll']);
    route::get('/search/{name}', [roleController::class, 'search']);
    route::get('/viewPermissinInRole/{id}',[roleController::class, 'viewPermissinInRole']);
     route::get('/addPermissionToRole/{id}/{id1}',[roleController::class, 'addPermissionToRole']);

});

route::group(
    [
//        'middleware'=>'auth.guard:employee-api',
        'prefix'=>'admin'
    ],function() {
Route::resource('role-permission',RoleAndPermissionController::class);
       route::post('/add',[RoleAndPermissionController::class, 'store']);
        route::post('/delete/{id}',[RoleAndPermissionController::class, 'destroy']);
    route::get('/show',[RoleAndPermissionController::class, 'index']);
    route::get('/create',[RoleAndPermissionController::class, 'create']);
    route::get('/edit/{id}',[RoleAndPermissionController::class, 'edit']);
    route::get('/update/{id}',[RoleAndPermissionController::class, 'update']);
    route::get('/addPermissionToRole/{id}/{id1}',[roleController::class, 'addPermissionToRole']);
});


##################### Order ################################
route::group([
    'middleware'=>'auth.guard:employee-api',
    'prefix'=>'order'
],function() {
    route::get('/showAllbyRole',[orderController::class, 'showAllbyRole']);
    route::get('/showAll',[orderController::class, 'showAll']);
    route::put('/update/{id}',[stockController::class, 'update']);
    route::post('/delete/{id}',[orderController::class, 'delete']);
    route::get('/edit/{id}',[orderController::class, 'edit']);
    route::get('/view/{id}',[orderController::class, 'view']);
    route::post('/note/{orderId}/{productId}',[orderController::class, 'productMessage']);
    route::put('/checkProduct/{orderId}',[orderController::class, 'checkProduct']);
    route::put('/reject/{id}',[orderController::class, 'reject']);
    route::post('/deleteItem/{orderId}/{itemId}',[orderController::class, 'deleteItem']);
    route::put('/decrementItem/{orderId}/{itemId}',[orderController::class, 'decrementItem']);
    route::put('/incrementItem/{orderId}/{itemId}',[orderController::class, 'incrementItem']);
    route::post('/addItem/{id}',[orderController::class, 'addItem']);
    route::get('/pickerOrders',[orderController::class, 'pickerOrders']);
    route::put('/confirm/{id}',[orderController::class, 'confirm']);
    route::put('/assignOrderToPicker/{id}',[orderController::class, 'assignOrderToPicker']);
    route::put('/confirmCredit/{id}',[orderController::class, 'confirmCredit']);
    route::put('/confirmSalesManager/{id}',[orderController::class, 'confirmSalesManager']);
    route::put('/confirmWarehouse/{id}',[orderController::class, 'confirmWarehouse']);
    route::put('/confirmPicked/{id}',[orderController::class, 'confirmPicked']);
    route::put('/confirmShipping/{id}',[orderController::class, 'confirmShipping']);
    route::post('/confirm',[orderController::class, 'confirm']);
    route::get('/showOrdersForCridet',[orderController::class, 'showOrdersForCridet']);
    route::get('/showOrdersForSales',[orderController::class, 'showOrdersForSales']);
    route::get('/showOrdersForwarehouse',[orderController::class, 'showOrdersForwarehouse']);
//    route::get('/viewInvoice/{id}',[orderController::class, 'viewInvoice']);
    route::post('/storeBarcode',[orderController::class, 'storeBarcode']);
    route::post('/storeBarcodeforOrder',[orderController::class, 'storeBarcodeforOrder']);




});
route::post('order/add',[orderController::class, 'add'])->middleware('auth.guard:customer-api');

route::group([
    'middleware'=>'auth.guard:pickers-api',
    'prefix'=>'order'],function() {
route::put('/scan/{orderId}/{itemId}',[orderController::class, 'scan']);
route::get('/start/{orderId}',[orderController::class, 'start']);
route::get('/End/{orderId}',[orderController::class, 'End']);
route::get('/pickerISOrders',[orderController::class, 'pickerISOrders']);
route::get('pickerOrdersComplete',[orderController::class, 'pickerOrdersComplete']);
route::get('pickerOrdersNotComplete',[orderController::class, 'pickerOrdersNotComplete']);
//route::get('/generateBarcode/{id}',[orderController::class, 'generateBarcode']);
route::get('/viewPickedQuantity/{orderId}/{itemId}',[orderController::class, 'viewPickedQuantity']);



});

route::get('/picker/edit/{id}',[orderController::class, 'edit']);
route::get('/picker/editpicker/{id}',[orderController::class, 'editpicker']);


route::get('order/shippingISOrders',[orderController::class, 'shippingISOrders'])->middleware('auth.guard:shipping-api');

//Route::post('/send-notification',[pickerController::class,'sendWebNotification']);
//


Route::get('/test-online', function () {
    dd('i am online ^_^');
});
##################### INVOICE ################################
route::group([
    'middleware'=>'auth.guard:employee-api',
    'prefix'=>'invoice'],function() {
//    route::post('/create/{id}',[invoiceController::class, 'create']);
    route::get('/viewInvoice/{id}',[invoiceController::class, 'viewInvoice']);

});

//});
