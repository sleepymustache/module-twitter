<?php

namespace Module;
require 'vendor/twitteroauth-0.5.3/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

define(
	'TWITTER_CONSUMER_KEY',
	'857XlKo7iJSUo461EkuqpFE8s'
);

define(
	'TWITTER_CONSUMER_SECRET',
	'tCkXdlBud8aczffTsce62wdtnNdpl56hkNXmmCPnDiCjIDOphT'
);

class Twitter {
	public $access_token;

	public function __construct($access_token='') {
		if (is_array($access_token)) {
			$this->access_token = $access_token;
		}
	}

	private function _get_request_token () {
		if (isset($_SESSION['oauth_token']) && isset($_SESSION['oauth_token_secret'])) {
			return array(
				'oauth_token' => $_SESSION['oauth_token'],
				'oauth_token_secret' => $_SESSION['oauth_token_secret']
			);
		}

		throw new \Exception("Request tokens do not exist.");
	}

	private function _get_access_token() {
		if (isset($this->access_token)) {
			return $this->access_token;
		}

		if (isset($_SESSION['access_token'])) {
			$this->access_token = $_SESSION['access_token'];
			return $this->access_token;
		}

		throw new \Exception("Access Token does not exist");
	}

	public function is_authorized() {
		try {
			if ($this->_get_access_token()) {
				return true;
			}
		} catch (\Exception $e) {
			return false;
		}
	}

	public function authorize($callback) {
		$connection = new TwitterOAuth(
			TWITTER_CONSUMER_KEY,
			TWITTER_CONSUMER_SECRET
		);

		$request_token = $connection->oauth('oauth/request_token', array(
			'oauth_callback' => $callback
		));

		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

		$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

		header('location: ' . $url);
	}

	public function authorize_callback() {
		$this->is_authorized = true;

		// The user is now signed in
		$request_token = $this->_get_request_token();

		// Ask for the access token
		$connection = new TwitterOAuth(
			TWITTER_CONSUMER_KEY,
			TWITTER_CONSUMER_SECRET,
			$request_token['oauth_token'],
			$request_token['oauth_token_secret']
		);

		$_SESSION['access_token'] = $connection->oauth(
			"oauth/access_token",
			array(
				"oauth_verifier" => $_REQUEST['oauth_verifier']
			)
		);

		$this->access_token = $_SESSION['access_token'];
	}

	public function get_user() {
		return $this->request('account/verify_credentials');
	}

	public function get_user_tweets($user) {
		return $this->request('statuses/user_timeline', array(
			'screen_name' => $user
		));
	}

	public function request($url, $options=[]) {
		$access_token = $this->_get_access_token();

		$connection = new TwitterOAuth(
			TWITTER_CONSUMER_KEY,
			TWITTER_CONSUMER_SECRET,
			$access_token['oauth_token'],
			$access_token['oauth_token_secret']
		);

		return $connection->get($url, $options);
	}
}