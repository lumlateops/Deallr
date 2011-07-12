<?php

class Application_Model_Account
{
	private $_session;
	private $_email;
	private $_provider;
	
	function __construct($email, $provider)
	{
		$auth_session = Zend_Registry::get('auth_session');
		$this->_session = $auth_session;
		$this->_email = $email;
		$this->_provider = $provider;
	}
	
	public function add()
	{		
		$url = "";
		if( isset( $this->_session ) && isset( $this->_session->auth_user['id'] ) ) 
		{
			$service_params = array(
				'userId' => $this->_session->auth_user['id'],
				'email' => $this->_email,
				'provider' => $this->_provider
			);
			$api_request = new Application_Model_APIRequest( array('account', 'add'), $service_params );
			$api_request->setMethod( Application_Model_APIRequest::METHOD_POST );
			error_log("Coming here");
			$api_response = $api_request->call();
			if( isset( $api_response ) && isset( $api_response['AuthUrl'][0] ) )
			{
				$url = $api_response['AuthUrl'][0];
			}
		}
		
		return $url;
	}
}	