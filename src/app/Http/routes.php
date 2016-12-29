<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) 
{
    return $app->version();
});

/**
* Routes Authorized used only for authorized
*/
include('routes_authorized.php');

/**
* Routes Authorized used only for 'office/store' resource
*/
include('routes_protected_resource.php');

/**
* Routes Protected used only 'my' resource
*/
include('routes_private_resource.php');

/**
* Routes Authorized used only for registered client public
*/
include('routes_public.php');

/**
* Routes to send mail
*/
include('routes_mail.php');
