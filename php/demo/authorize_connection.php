<?php
// Here's a sample application.

// To change api server... uses api.matchmove.com if this is ignored.
// define('KUNGFU_DOMAIN', 'beta8.api.matchmove.com');

// include the appkungfu initialize
require '../init.php';

// this is my class :)
require 'html.php';

/**
 * create a new AppKunfu instance set the domain of to login.
 * matchmove has a lot of partner domains. It is required to use the apropriate
 * partner domain. If ignored, automatically uses matchmove.com.
 **/
$mmad = new Kungfu('www.matchmove.com');

// check if an auth token is stored or if url contains a signed request from matchmove
if (!$mmad->connect->initialize() && empty($_GET))
{
    // oh. no auth token was found. request authorization.
    $mmad->connect->authorize();
}

/**
 * invoke a GET request to an api.
 *
 * @param $api string  api to call
 * @param $data array  data to be passed.
 **/
$users = $mmad->read('users');

// get the last request.
//var_dump($mmad->last_request->url());

/**
 * invoke a POST request to an api.
 *
 * @param $api string  api to call
 * @param $data array  data to be passed.
 **/
$comment = $mmad->create('users/feeds/status',
    array(
        'to_id' => '333333',
        'message' => 'Hello! this is coming from Kungfu SDK.'
    ));


//this is how you perform an update or PUT request
/**
 * invoke a PUT request to an api.
 *
 * @param $api string  api to call
 * @param $data array  data to be passed.
 **/
/*
$details = $mmad->update('api',
    array(
        'full_name' => 'Oh! I know how to use Kungfu SDK!'
    ));
*/

//this is how you perform an delete request
/**
 * invoke a DELETE request to an api.
 *
 * @param $api string  api to call
 * @param $data array  data to be passed.
 **/
$details = $mmad->delete('users/friends/requests',
    array(
        'friend_id' => '222222'
    ));

// now output the results! :)
echo '<h3>Read User</h3>';
html::table($users);

echo '<h3>Post status feed to self</h3>';
html::table($comment);

echo '<h3>Reject a friend request!</h3>';
html::table($details);