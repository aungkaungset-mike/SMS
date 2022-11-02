<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;

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
Route::resource('/role', RoleController::class);

Route::get('/roles-permissions', [RolePermissionController::class ,'roles'])->name('roles-permissions');
Route::get('/role-create', [RolePermissionController::class, 'createRole'])->name('role.create');
Route::post('/role-store', [RolePermissionController::class, 'storeRole'])->name('role.store');
Route::get('/role-edit/{id}', [RolePermissionController::class, 'editRole'])->name('role.edit');
Route::put('/role-update/{id}', [RolePermissionController::class, 'updateRole'])->name('role.update');

Route::get('/permission-create', [RolePermissionController::class, 'createPermission'])->name('permission.create');
Route::post('/permission-store', [RolePermissionController::class, 'storePermission'])->name('permission.store');
Route::get('/permission-edit/{id}', [RolePermissionController::class, 'editPermission'])->name('permission.edit');
Route::put('/permission-update/{id}', [RolePermissionController::class, 'updatePermission'])->name('permission.update');
