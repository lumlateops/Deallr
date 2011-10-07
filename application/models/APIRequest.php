<?php

class Application_Model_APIRequest
{
	const METHOD_POST = 1;
	const METHOD_GET = 2;
	
	private $_end_point;
	private $_params;
	private $_host;
	private $_secret_key;
	private $_method;
	
	public $request_url;
	public $response;
	
	/**
	 * Initializes the api request object
	 *
	 * @param array $end_point end point route .e.g, array( "account", "add" )
	 * @param array $params Array of key value pairs to be used as the service parameters
	 *
	 */
	function __construct( $end_point, $params = array() )
	{
		if( empty( $end_point ) || !count( $end_point ) )
		{
			throw Exception( 'End point is mandatory for making a web service call.' );
		}
		
/*
		if( empty( $params ) || !count( $params ) )
		{
			throw Exception( 'Parameters are mandatory for making a web service call.' );
		}
*/
		
		$this->_end_point = $end_point;
		$this->_params = $params;
		
		$config = Zend_Registry::get('config');
		$this->_host = $config->service->host;
		$this->_secret_key = $config->service->secretKey;
		$this->_method = self::METHOD_GET;
		
		return $this;
	}
	
	function setMethod($method)
	{
		$this->_method = $method;
	}
	
	/**
	 * Makes the web service call and returns response or error
	 *
	 * @return mixed array or bool Response from 
	 * @todo Add encoding and authentication
	 */
	function call()
	{
		$end_point_url = $this->_getEndPointURL();
		$service_params = $this->_params;
		$formatted_params_arr = array();
		$final_params_str = '';
		$request_url = '';
		
        // action body
		$ch = curl_init();
		
		$request_url = $this->_host.$end_point_url;
		
		if( $this->_method == self::METHOD_POST )
		{
			foreach( $service_params as $key => $value )
			{
				$formatted_params_arr[] = $key.'='.$value;
			}

			$final_params_str = implode('&', $formatted_params_arr);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $final_params_str );
			curl_setopt( $ch, CURLOPT_POST, TRUE );
			error_log('POST Params = '.$final_params_str);
		}
		else
		{
			foreach( $service_params as $key => $value )
			{
				$formatted_params_arr[] = $value;
			}

			$final_params_str = implode( '/', $formatted_params_arr );
			error_log('GET Params = '.$final_params_str);
			$request_url .= ( $final_params_str ? '/' : '' ).$final_params_str;
		}
		
		// set URL and other appropriate options
		curl_setopt( $ch, CURLOPT_URL, $request_url );
		curl_setopt( $ch, CURLOPT_HEADER, false);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
		
		// grab URL and pass it to the browser
		$response = curl_exec( $ch );
		
		// close CURL resource, and free up system resources
		$this->request_url = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL);// $request_url;
		error_log( $this->request_url );
		curl_close( $ch );
		
		if( !trim( $response ) )
		{
			throw new Exception( 'No response received from the web service. Server seems down. Request URL '.$request_url );
		}
		
		$this->response = $response;
		error_log( $this->response );
		$response = json_decode( $response, true );
		
		$framework_err_messages = array();
		$service_err_messages = array();
		$final_response = '';
		
		if( !$response["service"]["request"]["isValid"] )
		{
			error_log( $this->response );
			foreach( $response["service"]["errors"] as $error )
			{
				$framework_err_messages[] = $error["error"]["message"];
			}
			
			throw new Exception( implode( ' ', $framework_err_messages ) );
		}
		else if( isset( $response["service"]["errors"] ) ) 
		{
			error_log( $this->response );
			//Request was valid if the code reaches here. Check for service level errors.
			foreach( $response["service"]["errors"] as $error )
			{
				$service_err_messages[] = $error["error"]["code"];
			}
			throw new Exception( implode( ' ', $service_err_messages ) );			
		}
		else //Everything is valid and we have received the data back
		{
			$final_response = isset( $response["service"]["response"] ) ? $response["service"]["response"] : array();
 		}
		
		return $final_response;
	}
	
	/**
	 * Converts the end point parameters into URL suffix for the service call
	 *
	 * @return string
	 */
	private function _getEndPointURL()
	{
		return implode( "/", $this->_end_point );
	}
}

?>
