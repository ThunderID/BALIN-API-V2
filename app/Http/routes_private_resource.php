<?php

/**
*
* Routes For Private User
*
*
* Here is where you can register all of the routes for Private resources who can be accessed only by `me`.
*
* MY SUMMARY				: Line 23 - 62 
*
* MY PRODUCT 				: Line 67 - 83
*	
* MY PURCHASE ORDER 		: Line 88 - 110
*/

$app->group(['middleware' => 'jwt|me', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// MY SUMMARY
	// ------------------------------------------------------------------------------------
	$app->get('/me/{user_id}',
		[
			'uses'				=> 'MyController@detail'
		]
	);

	$app->get('/me/{user_id}/points',
		[
			'uses'				=> 'MyController@points'
		]
	);

	$app->get('/me/{user_id}/invitations',
		[
			'uses'				=> 'MyController@invitations'
		]
	);

	$app->get('/me/{user_id}/addresses',
		[
			'uses'				=> 'MyController@addresses'
		]
	);

	$app->post('/me/{user_id}/update',
		[
			'uses'				=> 'MyController@store'
		]
	);

	$app->post('/me/{user_id}/redeem',
		[
			'uses'				=> 'MyController@redeem'
		]
	);

	$app->post('/me/{user_id}/invite',
		[
			'uses'				=> 'MyController@invite'
		]
	);

	// ------------------------------------------------------------------------------------
	// MY PRODUCT
	// ------------------------------------------------------------------------------------
	$app->get('/me/{user_id}/products/recommended',
		[
			'uses'				=> 'MyProductController@recommended'
		]
	);

	$app->get('/me/{user_id}/products/purchased',
		[
			'uses'				=> 'MyProductController@purchased'
		]
	);

	$app->get('/me/{user_id}/products/viewed',
		[
			'uses'				=> 'MyProductController@viewed'
		]
	);

	// ------------------------------------------------------------------------------------
	// MY PURCHASE ORDER
	// ------------------------------------------------------------------------------------
	$app->get('/me/{user_id}/orders',
		[
			'uses'				=> 'MyOrderController@index'
		]
	);

	$app->get('/me/{user_id}/order/{order_id}',
		[
			'uses'				=> 'MyOrderController@detail'
		]
	);

	$app->get('/me/{user_id}/order/number/{refnumber}',
		[
			'uses'				=> 'MyOrderController@refnumber'
		]
	);

	$app->get('/me/{user_id}/incart',
		[
			'uses'				=> 'MyOrderController@incart'
		]
	);

	$app->post('/me/{user_id}/order/store',
		[
			'uses'				=> 'MyOrderController@store'
		]
	);
});
