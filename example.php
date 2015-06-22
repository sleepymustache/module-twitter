<?php
/**
 * This is an example on how to use the Twitter class.
 */

namespace Module;

require_once('class.twitter.php');

// If we didn't call this script, don't run it on load
if (basename(__FILE__) != basename($_SERVER["SCRIPT_FILENAME"]) ) {
  return false;
}

// Give brief directions on how to properly use the script
if (!isset($_GET['at'])) {
	echo "To use this add ?at={screen_name} to the url";
	die();
} else {
	$at = $_GET['at'];
}

// This is how you pass the oauth_token to the twitter class
$twitter = new Twitter(Array(
	'oauth_token' => '40300253-jDdH8Hvbo8q3LNEPW5WtN2DeVOsGCyfCYTAKnMUbh',
	'oauth_token_secret' => 'zW2Mea8So2xz3lInN34jUYevwfJuOsIbQMNtqP15H9M8J'
));

echo json_encode($twitter->get_user_tweets($at));
