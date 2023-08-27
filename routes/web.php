<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\orderController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\QRcode;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
route::get('/generateBarcode',[orderController::class, 'generateBarcode']);



//Route::get('/qr', function () {
//    $qr=QrCode::format('png')->generate('Make me into a QrCode!');
//    Storage::disk('local')->put('qrcode.png',$qr);
//    return $qr;
//});

Route::post('/send-notification',[Controller::class,'sendWebNotification']);

Route::get('/firebase', function () {
    return view('firebase');
});
