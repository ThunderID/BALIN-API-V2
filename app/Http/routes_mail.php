<?php

/**
*
* Routes For mail
*
*
* ORDER
*
* Invoice 					: Line 41 
*	
* Paid 						: Line 47
*	
* Shipped 					: Line 53
*	
* Delivered 				: Line 59
*	
* Canceled 					: Line 65
*	
* ACCOUNT
*	
* Reset Password 			: Line 75
*	
* Send Invitation 			: Line 81
*	
* CRM
*	
* Welcome					: Line 91
*	
* Abandon					: Line 97
*	
* Contact					: Line 103
*/

$app->group(['middleware' => 'jwt', 'namespace' => 'App\Http\Controllers\Mail', 'prefix' => 'mail'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// ORDER
	// ------------------------------------------------------------------------------------

	$app->post('/invoice',
		[
			'uses'				=> 'OrderController@invoice'
		]
	);

	$app->post('/paid',
		[
			'uses'				=> 'OrderController@paid'
		]
	);

	$app->post('/shipped',
		[
			'uses'				=> 'OrderController@shipped'
		]
	);

	$app->post('/delivered',
		[
			'uses'				=> 'OrderController@delivered'
		]
	);

	$app->post('/canceled',
		[
			'uses'				=> 'OrderController@canceled'
		]
	);

	// ------------------------------------------------------------------------------------
	// ACCOUNT
	// ------------------------------------------------------------------------------------

	$app->post('/password/reset',
		[
			'uses'				=> 'AccountController@password'
		]
	);

	$app->post('/invitation',
		[
			'uses'				=> 'AccountController@invitation'
		]
	);

	// ------------------------------------------------------------------------------------
	// CRM
	// ------------------------------------------------------------------------------------

	$app->post('/welcome',
		[
			'uses'				=> 'CRMController@welcome'
		]
	);

	$app->post('/abandoned',
		[
			'uses'				=> 'CRMController@abandoned'
		]
	);

	$app->post('/contact',
		[
			'uses'				=> 'CRMController@contact'
		]
	);
});