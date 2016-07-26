<?php

/**
*
* Routes For Public Resource
*
*
* Here is where you can register all of the routes for Public resources who can be accessed by everyone from registered engine.
*
* AUTHENTICATE				: Line 28 - 62 
*
* PRODUCTS 					: Line 67 - 71
*
* CLUSTERS 					: Line 76 - 80
*
* LABELS 					: Line 85 - 89
*
* STORE 					: Line 94 - 98
*
* EXTENSIONS				: Line 103 - 107
*/

$app->group(['middleware' => 'oauth', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// AUTHENTICATE
	// ------------------------------------------------------------------------------------
	$app->post('/customer/sign/in',
		[
			'uses'				=> 'AuthController@signin'
		]
	);

	$app->post('/customer/sign/up',
		[
			'uses'				=> 'AuthController@signup'
		]
	);

	$app->post('/customer/activate',
		[
			'uses'				=> 'AuthController@activate'
		]
	);

	$app->post('/customer/forgot/password',
		[
			'uses'				=> 'AuthController@forgot'
		]
	);

	$app->get('/customer/reset/{link}',
		[
			'uses'				=> 'AuthController@reset'
		]
	);

	$app->post('/customer/change/password',
		[
			'uses'				=> 'AuthController@change'
		]
	);

	// ------------------------------------------------------------------------------------
	// PRODUCTS
	// ------------------------------------------------------------------------------------
	$app->get('/balin/public/products',
		[
			'uses'				=> 'UIController@products'
		]
	);

	// ------------------------------------------------------------------------------------
	// CLUSTERS
	// ------------------------------------------------------------------------------------
	$app->get('/balin/public/clusters/{type}',
		[
			'uses'				=> 'UIController@clusters'
		]
	);

	// ------------------------------------------------------------------------------------
	// LABELS
	// ------------------------------------------------------------------------------------
	$app->get('/balin/public/labels',
		[
			'uses'				=> 'UIController@labels'
		]
	);

	// ------------------------------------------------------------------------------------
	// STORE
	// ------------------------------------------------------------------------------------
	$app->get('/balin/public/config',
		[
			'uses'				=> 'UIController@config'
		]
	);

	// ------------------------------------------------------------------------------------
	// EXTENSIONS
	// ------------------------------------------------------------------------------------
	$app->get('/balin/public/extensions',
		[
			'uses'				=> 'UIController@extensions'
		]
	);
});

// // ------------------------------------------------------------------------------------
// // VERITRANS
// // ------------------------------------------------------------------------------------
// $app->get('/veritrans/validate',
// 	[
// 		'uses'				=> 'PaymentController@veritranscc'
// 	]
// );
