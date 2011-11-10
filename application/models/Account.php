<?php

class Application_Model_Account
{
	private $_session;
	private $_provider;
	
	function __construct($provider)
	{
		$auth_session = Zend_Registry::get('auth_session');
		$this->_session = $auth_session;
		$this->_provider = $provider;
	}
	
	public function add()
	{
		$url = '';
		if (isset($this->_session) && isset($this->_session->auth_user['id'])) {
			$service_params = array(
				'userId' => $this->_session->auth_user['id'],
				'provider' => $this->_provider
			);
			
			$api_request = new Application_Model_APIRequest( array('account', 'add'), $service_params );
			$api_request->setMethod( Application_Model_APIRequest::METHOD_POST );
			$api_response = $api_request->call();
			if (isset($api_response) && isset($api_response['AuthUrl'][0])) {
				 $url = $api_response['AuthUrl'][0];
				 $secret = $api_response['tokenSecret'][0];
				 $this->_session->token_secret = $secret;
			}
		}
		
		return $url;
	}
	
	public function upgradeToken($verifier, $token)
	{
		$status = false;
		$email = '';
		
		if (isset($this->_session) && isset($this->_session->auth_user['id'])) {
			$service_params = array(
				'userId' => $this->_session->auth_user['id'],
				'tokenSecret' => $this->_session->token_secret,
				'provider' => $this->_provider,
				'queryString' => rawurlencode('oauth_verifier='.$verifier.'&oauth_token='.$token)
			);
			$api_request = new Application_Model_APIRequest( array('account', 'upgradeEmailToken'), $service_params );
			$api_request->setMethod( Application_Model_APIRequest::METHOD_POST );
			$api_response = $api_request->call();
			if (isset($api_response) && isset($api_response['Message'][0]) && isset($api_response['email'][0])) {
				$status = true;
				$email = $api_response['email'][0];
				// Clear token secret once the call is successful.
				unset($this->_session->token_secret);
			}
		}
		
		return array('status' => $status, 'email' => $email);
	}
}	