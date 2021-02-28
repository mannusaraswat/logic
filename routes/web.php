<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use PDF as PP;
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


Route::get('/pdf', function () {
     $data = [
            'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('m/d/Y')
        ];
        $pdf = PP::loadView('myPDF', $data);
        $name = 'new.pdf';
        Storage::put("public/pdf/$name", $pdf->output());
die('complete');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// download pdf
Route::get('download', [App\Http\Controllers\EmailController::class, 'index'])->name('download')->middleware('signed');
