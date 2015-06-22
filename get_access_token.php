<?php
/**
 * This script will generate an access token for use with the twitter module
 */

namespace Module;

// If we didn't call this script, don't run it on load
if (basename(__FILE__) != basename($_SERVER["SCRIPT_FILENAME"]) ) {
  return false;
}

session_start();
require_once('class.twitter.php');
$twitter = new Twitter();

// If we are already authorized, don't do another call, just give the token
if ($twitter->is_authorized()) {
	$status['code'] = 200;
	$status['description'] = "Authorization Successful";
	$status['access_token'] = $twitter->access_token;
	echo json_encode($status);
} else {
	// if we are not authorized, ask the user to sign in and get a token
	if (!isset($_GET['oauth_token'])) {
		$twitter->authorize('http://' . $_SERVER['HTTP_HOST'] . '/app/modules/twitter/get_access_token.php');
	} else {
		// The user has now signed in, get the token and display it.
		$twitter->authorize_callback();
		$status['code'] = 200;
		$status['description'] = "Authorization Successful";
		$status['access_token'] = $twitter->access_token;
		echo json_encode($status);
	}
}


