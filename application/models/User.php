<?php

class Application_Model_User
{
	const PASS_SECRET = 'mvSOo8GveYKoO4YRfz7j';
	public static function add($params)
	{
		$password = hash_hmac("sha256", $params['password'], self::PASS_SECRET, true);
		$service_params = array(
			'username' => $params['username'],
			'password' => $password,
			'gender' => $params['gender'],
			'fbFullName' => $params['name'],
			'fbEmailAddress' => $params['email'],
			'fbUserId' => $params['user_id'],
			'fbLocationName' => isset( $params['location']['name'] ) ? $params['location']['name'] : '',
			'fbLocationId' => isset( $params['location']['id'] ) ? $params['location']['id'] : ''
		);
		$api_request = new Application_Model_APIRequest( array('user', 'add'), $service_params );
		$api_request->setMethod( Application_Model_APIRequest::METHOD_POST );
		return $api_request->call();
	}
}

