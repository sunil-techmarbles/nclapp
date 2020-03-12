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
Route::get('/logout', 'LoginController@logout')->name('logout');
Route::get('/ForgetPassword', 'LoginController@forgetPassword')->name('forgetPassword');
Route::post('/SendPasswordResetEmail', 'LoginController@sendPasswordResetEmail')->name('sendPasswordResetEmail');
Route::get('/ResetPasswordForm/{token}', 'LoginController@resetPasswordForm')->name('resetPasswordForm');
Route::post('ResetPassword', 'LoginController@resetPassword')->name('resetPassword');

Route::middleware(['guest','revalidate'])->group(function () {
	Route::get('/','LoginController@index')->name('login.view');
	Route::get('/login','LoginController@index')->name('login.view');
	Route::post('/authenticate','LoginController@loginAuthenticate')->name('login.authenticate');
	Route::get('/register', 'RegisterController@index')->name('user.register');
	
	Route::prefix('admin')->group(function () {
		Route::get('/dashboard','DashboardController@index')->name('dashboard');
	});
});

Route::get('/logout', 'LoginController@logout')->name('logout');
// Change header for reports, import and export
Route::middleware(['changereportheader', 'checkadminpermissions'])->group(function () {
	Route::prefix('admin')->group(function () {
		// refub 
		Route::get('/exportcoareport','RefurbController@ExportcoaReport')->name('get.coa.report');
		Route::get('/exportissuereport','RefurbController@ExportIssueReport')->name('get.issue.report');
		// supplies 
		Route::get('/exportsupplies','SuppliesController@exportSupplies')->name('export.supplies');
		Route::post('/importsupplies','SuppliesController@importSupplies')->name('import.supplies');
	});
});

Route::middleware(['checkadminpermissions','revalidate'])->group(function () {
	Route::prefix('admin')->group(function () {

	    Route::get('/users', 'UsersController@index')->name('users');
	    Route::get('/edituser/{userid}', 'UsersController@edituser')->name('edit.user');
	    Route::post('/edituserHandle/{userid}', 'UsersController@edituserHandle')->name('edit.edituserHandle');
	    Route::get('/DeleteUser/{userid}', 'UsersController@DeleteUser');
	    
	    Route::any('/audit','AuditController@index')->name('audit');
		Route::get('/addpartnumber','AuditController@AddPartNumber')->name('audit.add.part.number');

		Route::get('/supplies','SuppliesController@index')->name('supplies');
		Route::get('/addsupplie','SuppliesController@addSupplies')->name('add.supplies');
		Route::get('/updateqtyreorder','SuppliesController@updateQtyReorder')->name('update.qty.reorder');
		Route::post('/storesupplie','SuppliesController@storeSupplies')->name('store.supplies');
		Route::get('/editsupplie/{supplieid}','SuppliesController@editSupplies')->name('edit.supplies');
		Route::post('/updatesupplie','SuppliesController@updateSupplies')->name('update.supplies');
		Route::get('/deletesupplie/{supplieid}','SuppliesController@deleteSupplie')->name('delete.supplies');
     
		Route::get('/asin','AsinController@index')->name('asin');
		Route::get('/addasin','AsinController@addAsins')->name('add.asins');
		Route::post('/storeasin','AsinController@storeAsins')->name('store.asin');
		Route::post('/updateasin','AsinController@updateAsins')->name('update.asin');
		Route::get('/editasin/{asinid}','AsinController@editAsin')->name('edit.asin');
		Route::get('/asinparts/{asinid}','AsinController@partsAsin')->name('parts.asin');
		Route::get('/deleteasin/{asinid}','AsinController@deleteAsin')->name('delete.asin');
		Route::get('/partlookup','AsinController@PartLookup')->name('part.lookup');

		Route::get('/refurb','RefurbController@index')->name('refurb');
		Route::get('/getasset','RefurbController@getAsset')->name('get.asset');
		Route::post('/saveasin','RefurbController@saveAsin')->name('save.asin');
		Route::post('/savecoa','RefurbController@saveCOA')->name('save.coa');
		Route::post('/saveissue','RefurbController@saveIssue')->name('save.issue');
		Route::any('/setwholesale','RefurbController@setWholesale')->name('set.wholesale');
		Route::post('/saveprint','RefurbController@savePrint')->name('save.print');
		Route::post('/checkcoa','RefurbController@checkCOA')->name('check.coa');
		Route::post('/saveprint','RefurbController@savePrint')->name('save.print');
		
		//In bound
		Route::any('/packages','PackageController@index')->name('packages');  
		Route::post('/addnewpackage','PackageController@AddNewPackage')->name('add.package');
		Route::get('/checkinpackage','PackageController@CheckInPackage')->name('checkIn.package');  

		//Out bound
		Route::get('/shipments','ShipmentController@index')->name('shipments');
		Route::post('/addshipment','ShipmentController@addShipment')->name('add.shipment');

		Route::any('/sessions','SessionController@index')->name('sessions');
		Route::get('/checktravelerid', 'AuditController@checkTravelerId')->name('check.traveler.id');
		Route::get('/gettab', 'AuditController@getTab')->name('get.tab');
		Route::get('/checktraveleridformobile', 'AuditController@CheckTravelerIdForMobile')->name('check.traveler.id.for.mobile');
		Route::get('/getmodels', 'AuditController@getModels')->name('get.models');
		Route::get('/loadmodel', 'AuditController@loadModel')->name('load.model');
		// Route::get('/savepartnumber', 'AuditController@savePartNumber')->name('save.part.number');
		Route::get('/getfiles', 'AuditController@getFiles')->name('get.files');
		Route::get('/loadxml', 'AuditController@loadXML')->name('load.xml');
		Route::get('/loadlast', 'AuditController@loadLast')->name('load.last');
		Route::get('/getrefnotification', 'AuditController@getRefNotification')->name('load.last');
		Route::get('/getpreview', 'AuditController@getPreview')->name('get.preview');
		Route::post('/storeauditrecord', 'AuditController@storeAuditRecord')->name('store.audit.record');

		Route::any('/inventory', 'ShopifyController@index')->name('inventory');
		Route::get('/getasin', 'AsinController@getASINNumber')->name('getasin');
		Route::get('/setmodelid', 'ShopifyController@setModelId')->name('setmodelid');
	});
});
