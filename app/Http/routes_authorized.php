<?php

/**
*
* Routes For Authorized User
*
*
* Here is where you can register all of the routes for authenticate user or apps.
*
*
* Routes For Authenticate User : Line 30
*
* Method 			: Post 
*
* Parameter Send 	: [email => 'xx', password => 'yy', grant_type => ('password' or 'refresh_token'), client_id => 'xx', client_secret => 'xx']
*
* Returned JSend 	: [token => [access_token => 'xx', refresh_token => 'xx', token_type => 'Bearer', expires_in => 00], me => [id => 'xx', email => 'xx', name => 'xx', role => 'xx', quota_referral => 00, reference_name => 'xx', total_point => 00, total_reference => 00]]
*
*
*
* Routes For Authenticate Client : Line 47
*
* Method 			: Post 
*
* Parameter Send 	: [grant_type => 'client_credentials', client_id => 'xx', client_secret => 'xx']
*
* Returned JSend 	: [token => [access_token => 'xx', token_type => 'Bearer', expires_in => 00]]
*/

$app->post('/oauth/access_token', function() 
{
	$issue['token']								= \LucaDegasperi\OAuth2Server\Facades\Authorizer::issueAccessToken();

	if(\Illuminate\Support\Facades\Auth::check())
	{
		$issue['me']							= \Illuminate\Support\Facades\Auth::user()->toArray();

		return new \App\Libraries\JSend('success', (array)$issue);
	}
	else
	{
		return new \App\Libraries\JSend('error', (array)['No Data'], 'User invalid :( ');
	}

});

$app->post('/oauth/client/access_token', function() 
{
	$issue['token']								= \LucaDegasperi\OAuth2Server\Facades\Authorizer::issueAccessToken();
	
	return new \App\Libraries\JSend('success', (array)$issue);
});

$app->group(['middleware' => 'oauth', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// Gettin' Me
	// ------------------------------------------------------------------------------------

	$app->get('/me', function() 
	{
		$user 								= \LucaDegasperi\OAuth2Server\Facades\Authorizer::getResourceOwnerId();

		return $user;
	});
});

