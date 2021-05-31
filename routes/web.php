<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
=======
use App\Http\Controllers\GetJsonController;
>>>>>>> 85a3fb97b6e587499bdaa5ec6c3b1015194f4a96

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
<<<<<<< HEAD
=======

Route::get('/getjson/', [GetJsonController::class, 'getJSON']);
>>>>>>> 85a3fb97b6e587499bdaa5ec6c3b1015194f4a96
