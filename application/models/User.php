<?php

class Application_Model_User
{
	const PASS_SECRET = 'mvSOo8GveYKoO4YRfz7j';
	
	private $_hasAuthorizedEmailAccounts;
	private $_username;
	
	public static function add($params)
	{
		$reg_params = $params['registration'];
		$password = hash_hmac("sha256", $reg_params['password'], self::PASS_SECRET, true);
		$service_params = array(
			'username' => $reg_params['username'],
			'password' => $password,
			'gender' => $reg_params['gender'],
			'fbFullName' => $reg_params['name'],
			'fbEmailAddress' => $reg_params['email'],
			'fbUserId' => $params['user_id'],
			'fbLocationName' => isset( $reg_params['location']['name'] ) ? $reg_params['location']['name'] : '',
			'fbLocationId' => isset( $reg_params['location']['id'] ) ? $reg_params['location']['id'] : ''
		);
		$api_request = new Application_Model_APIRequest( array('user', 'add'), $service_params );
		$api_request->setMethod( Application_Model_APIRequest::METHOD_POST );
		return $api_request->call();
	}
	
	public function initWithFbUserId($fbUserId)
	{
        $api_request = new Application_Model_APIRequest( array('login'), array( 'fbUserId' => $fbUserId ) );
		$response = $api_request->call();
		$response = $response['user'][0];
		if( isset( $response['username'] ) && trim( $response['username'] ) )
		{
			$this->_username = $response['username'];
			$this->_id = $response['id'];
			$this->_hasAuthorizedEmailAccounts = isset( $response['hasSetupEmailAccounts'] ) ? !!$response['hasSetupEmailAccounts'] : false;
		}
		
		return $this;
	}
	
	public function hasAuthorizedEmailAccounts()
	{
		return !!$this->_hasAuthorizedEmailAccounts;
	}
	
	public function username()
	{
		return $this->_username;
	}
	
	public function id()
	{
		return $this->_id;
	}
}

