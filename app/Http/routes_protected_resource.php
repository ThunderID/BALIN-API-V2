<?php

/**
*
* Routes For Protected User
*
*
* Here is where you can register all of the routes for protected resources who can be accessed based on ACL.
*
* STAFF AREA
*
* Product Resources 		: Line 49 - 71 
*	
* Product Warehouse Data 	: Line 77 - 93
*	
* Cluster Resources 		: Line 99 - 121
*	
* Supplier Resources 		: Line 127 - 149
*	
* Purchase Resources 		: Line 155 - 171
*	
* Sale Resources 			: Line 177 - 193
*	
* Courier Resources 		: Line 199 - 221
*	
* MANAGER AREA
*	
* Voucher Resources 		: Line 230 - 250
*	
* Point Resources 			: Line 257 - 267
*	
* Setting Resources 		: Line 273 - 289
*	
* ADMIN AREA
*	
* Administrator Resources	: Line 298 - 314
*	
* Customer Resources		: Line 320 - 330
*	
* Report					: Line 336 - 346
*/

$app->group(['middleware' => 'oauth|staff', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// PRODUCTS
	// ------------------------------------------------------------------------------------

	$app->get('/products',
		[
			'uses'				=> 'ProductController@index'
		]
	);

	$app->get('/product/{id}',
		[
			'uses'				=> 'ProductController@detail'
		]
	);

	$app->post('/product/store',
		[
			'uses'				=> 'ProductController@store'
		]
	);

	$app->delete('/product/delete/{id}',
		[
			'uses'				=> 'ProductController@delete'
		]
	);

	// ------------------------------------------------------------------------------------
	// PRODUCT WAREHOUSE
	// ------------------------------------------------------------------------------------

	$app->get('/product/stock/card/{id}',
		[
			'uses'				=> 'WarehouseController@card'
		]
	);

	$app->get('/products/stock/critical',
		[
			'uses'				=> 'WarehouseController@critical'
		]
	);

	$app->get('/products/stock/opname',
		[
			'uses'				=> 'WarehouseController@opname'
		]
	);

	// ------------------------------------------------------------------------------------
	// CLUSTERS
	// ------------------------------------------------------------------------------------

	$app->get('/clusters/{type}',
		[
			'uses'				=> 'ClusterController@index'
		]
	);

	$app->get('/cluster/{type}/{id}',
		[
			'uses'				=> 'ClusterController@detail'
		]
	);

	$app->post('/cluster/{type}/store',
		[
			'uses'				=> 'ClusterController@store'
		]
	);

	$app->delete('/cluster/{type}/delete/{id}',
		[
			'uses'				=> 'ClusterController@delete'
		]
	);

	// ------------------------------------------------------------------------------------
	// EXTENSIONS
	// ------------------------------------------------------------------------------------

	$app->get('/products/extensions',
		[
			'uses'				=> 'ProductExtensionController@index'
		]
	);

	$app->get('/products/extension/{id}',
		[
			'uses'				=> 'ProductExtensionController@detail'
		]
	);

	$app->post('/products/extension/store',
		[
			'uses'				=> 'ProductExtensionController@store'
		]
	);

	$app->delete('/products/extension/delete/{id}',
		[
			'uses'				=> 'ProductExtensionController@delete'
		]
	);

	// ------------------------------------------------------------------------------------
	// SUPPLIERS
	// ------------------------------------------------------------------------------------

	$app->get('/suppliers',
		[
			'uses'				=> 'SupplierController@index'
		]
	);

	$app->get('/supplier/{id}',
		[
			'uses'				=> 'SupplierController@detail'
		]
	);

	$app->post('/supplier/store',
		[
			'uses'				=> 'SupplierController@store'
		]
	);

	$app->delete('/supplier/delete/{id}',
		[
			'uses'				=> 'SupplierController@delete'
		]
	);

	// ------------------------------------------------------------------------------------
	// PURCHASES
	// ------------------------------------------------------------------------------------

	$app->get('/purchases',
		[
			'uses'				=> 'PurchaseController@index'
		]
	);

	$app->get('/purchase/{id}',
		[
			'uses'				=> 'PurchaseController@detail'
		]
	);

	$app->post('/purchase/store',
		[
			'uses'				=> 'PurchaseController@store'
		]
	);

	// ------------------------------------------------------------------------------------
	// SALES
	// ------------------------------------------------------------------------------------

	$app->get('/sales',
		[
			'uses'				=> 'SaleController@index'
		]
	);

	$app->get('/sale/{id}',
		[
			'uses'				=> 'SaleController@detail'
		]
	);

	$app->post('/sale/update/status',
		[
			'uses'				=> 'SaleController@status'
		]
	);

	// ------------------------------------------------------------------------------------
	// COURIERS
	// ------------------------------------------------------------------------------------

	$app->get('/couriers',
		[
			'uses'				=> 'CourierController@index'
		]
	);

	$app->get('/courier/{id}',
		[
			'uses'				=> 'CourierController@detail'
		]
	);

	$app->post('/courier/store',
		[
			'uses'				=> 'CourierController@store'
		]
	);

	$app->delete('/courier/delete/{id}',
		[
			'uses'				=> 'CourierController@delete'
		]
	);
});

$app->group(['middleware' => 'oauth|manager', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// VOUCHERS
	// ------------------------------------------------------------------------------------

	$app->get('/vouchers',
		[
			'uses'				=> 'VoucherController@index'
		]
	);

	$app->get('/voucher/{id}',
		[
			'uses'				=> 'VoucherController@detail'
		]
	);

	$app->post('/voucher/store',
		[
			'uses'				=> 'VoucherController@store'
		]
	);
	$app->delete('/voucher/delete/{id}',
		[
			'uses'				=> 'VoucherController@delete'
		]
	);
	
	// ------------------------------------------------------------------------------------
	// POINTS
	// ------------------------------------------------------------------------------------

	$app->get('/points',
		[
			'uses'				=> 'PointController@index'
		]
	);

	$app->post('/point/store',
		[
			'uses'				=> 'PointController@store'
		]
	);

	// ------------------------------------------------------------------------------------
	// SETTINGS
	// ------------------------------------------------------------------------------------
	
	$app->get('/settings/{type}',
		[
			'uses'				=> 'StoreSettingController@index'
		]
	);

	$app->get('/setting/{id}',
		[
			'uses'				=> 'StoreSettingController@detail'
		]
	);

	$app->post('/setting/store',
		[
			'uses'				=> 'StoreSettingController@store'
		]
	);
});

$app->group(['middleware' => 'oauth|admin', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// ADMINISTRATORS
	// ------------------------------------------------------------------------------------

	$app->get('/admins',
		[
			'uses'				=> 'AdminController@index'
		]
	);

	$app->get('/admin/{id}',
		[
			'uses'				=> 'AdminController@detail'
		]
	);

	$app->post('/admin/store',
		[
			'uses'				=> 'AdminController@store'
		]
	);

	// ------------------------------------------------------------------------------------
	// CUSTOMERS
	// ------------------------------------------------------------------------------------

	$app->get('/customers',
		[
			'uses'				=> 'CustomerController@index'
		]
	);

	$app->get('/customer/{id}',
		[
			'uses'				=> 'CustomerController@detail'
		]
	);

	// ------------------------------------------------------------------------------------
	// REPORTS
	// ------------------------------------------------------------------------------------

	$app->get('/report/usage/of/vouchers',
		[
			'uses'				=> 'ReportController@voucher'
		]
	);

	$app->get('/report/sold/products',
		[
			'uses'				=> 'ReportController@product'
		]
	);
});

$app->group(['middleware' => 'oauth|staff', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// TOOLS
	// ------------------------------------------------------------------------------------
	
	$app->get('/broadcast/queue',
		[
			'uses'				=> 'BroadcastController@queue'
		]
	);

	$app->post('/broadcast/price',
		[
			'uses'				=> 'BroadcastController@price'
		]
	);
});

// $app->group(['middleware' => 'jwt', 'namespace' => 'App\Http\Controllers'], function($app)  {
    $app->get('/try/to/add/to/cart', 'TrySaleController@addtocart');
// });


// $app->get('/try/to/add/to/cart',
// 	[
// 		'uses'				=> 'TrySaleController@addtocart'
// 	]
// );