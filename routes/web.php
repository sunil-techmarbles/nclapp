<?php
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

Route::post('/registerAuthenticate', 'RegisterController@registerAuthenticate')->name('register.registerAuthenticate');      
Route::middleware(['guest'])->group(function () { 
 
	Route::get('/','LoginController@index')->name('login.view');  
	
	Route::get('/login','LoginController@index')->name('login.view');
	
	Route::post('/authenticate','LoginController@loginAuthenticate')->name('login.authenticate');  

	Route::get('/register', 'RegisterController@index');      
}); 
 
Route::get('/logout', 'LoginController@logout')->name('logout'); 
Route::prefix('admin')->group(function () {

    Route::get('/users', 'UsersController@index');  
	Route::get('/dashboard','DashboardController@index')->name('dashboard'); 
	Route::get('/supplies','SuppliesController@index')->name('supplies');
	Route::get('/addsupplies','SuppliesController@addsupplies')->name('add.supplies');
	Route::get('/editsupplies','SuppliesController@editsupplies')->name('edit.supplies');
	Route::get('/exportsupplies','SuppliesController@exportsupplies')->name('export.supplies');
	Route::get('/importsupplies','SuppliesController@importsupplies')->name('import.supplies');
    
});


