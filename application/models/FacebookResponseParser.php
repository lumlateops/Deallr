<?php

class Application_Model_FacebookResponseParser
{
	private $_response;
	private $_secret_key;
	
	function __construct($signed_response)
	{
		$this->_response = $signed_response;
		
		$config = Zend_Registry::get('config');
		$this->_secret_key = $config->fb->appSecretKey;
	}

	public function parse()
	{
		list( $encoded_sig, $payload ) = explode( '.', $signed_request, 2 );
		
		// decode the data
		$signature = $this->_base64_url_decode( $encoded_sig );
		$data = json_decode( $this->_base64_url_decode( $payload ), true );
		
		if( strtoupper( $data['algorithm'] ) !== 'HMAC-SHA256' )
		{
			throw new Exception( 'Unknown algorithm used in FB response. Expected HMAC-SHA256' );
		}
		
		// check sigature
		$expected_sig = hash_hmac( 'sha256', $payload, $this->_secret_key, true );
		
		if( $signature !== $expected_sig ) {
			throw new Exception( 'Bad Signed JSON signature!' );
		}
		
		return $data
	}
	
	private function _base64_url_decode($input)
	{
		return base64_decode(strtr($input, '-_', '+/'));
	}
}

?>