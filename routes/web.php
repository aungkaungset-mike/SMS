<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\RoleController;

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
    return redirect('/login');
});
    
Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resource('/teacher', TeacherController::class);

Route::resource('/parent', ParentController::class);

Route::resource('/student', StudentController::class);

Route::resource('/class', ClassController::class);

Route::resource('/subject', SubjectController::class);

Route::resource('/subject', RoleController::class);
