<?php

class Application_Model_User
{
	const PASS_SECRET = 'mvSOo8GveYKoO4YRfz7j';
	const TIMEOUT_INTERVAL = 1800; //in seconds - 30 mins
	
	private $_session;
	
	public function __construct()
	{
		$auth_session = Zend_Registry::get('auth_session');
		$this->_session = $auth_session;
	}

	public static function logOut()
	{
		$auth_session = Zend_Registry::get('auth_session');
		
		if( isset($auth_session->auth_user) )
		{
			unset($auth_session->auth_user);
		}
		
		return true;
	}
	
	public static function isAuthenticated()
	{
		$auth_session = Zend_Registry::get('auth_session');
		if( isset($auth_session->auth_user) && isset( $auth_session->auth_user['fbUserId'] ) )
		{
			if( (time() - $auth_session->lastAuthCheck) < self::TIMEOUT_INTERVAL )
			{
				error_log('User is in the session');
				return true;
			}
			else
			{
				error_log('Session exists but it timed out. Current Time = '.time().' LastAuthCheck = '.$auth_session->lastAuthCheck.' Difference = '.(time() - $auth_session->lastAuthCheck));
			}
		}
		
		$auth_session->auth_user = array();
		
		error_log('Either session does not exist or it has timed out..Going to Facebook to check authentication');
		$config = Zend_Registry::get('config');
		
		//Go to Facebook to authenticate
		include_once(APPLICATION_PATH.'/../library/Facebook/facebook.php');
		$facebook = new Facebook(array( 
			'appId' => $config->fb->appID, 
			'secret' => $config->fb->appSecretKey
		));
		
		$uid = $facebook->getUser();
		error_log('User ID = '.$uid);
		
		if($uid)
		{
			//Has active session...try to fetch the user and his profile info
			try
			{
				$user = $facebook->api('/me');
			}
			catch(Exception $e)
			{
				//Tie tie fish
			}
			
			if(!empty($user))
			{
				error_log('User is authenticated and authorized..Sign in user');
				error_log( $facebook->getAccessToken() );

				try
				{
					$user_obj = new Application_Model_User();
					$user_obj->initWithFbUserId($user['id']);
					$auth_session->auth_user = array_merge( $auth_session->auth_user, array(
						'gender' => $user['gender'],
						'fbFullName' => $user['name'],
						'fbEmailAddress' => $user['email'],
						'fbUserId' => $user['id'],
						'fbLocationName' => isset( $user['location']['name'] ) ? $user['location']['name'] : '',
						'fbLocationId' => isset( $user['location']['id'] ) ? $user['location']['id'] : '',
						'fbAccessToken' => $facebook->getAccessToken()
					) );
					$auth_session->lastAuthCheck = time();
					error_log(json_encode( $auth_session->auth_user ));
				}
				catch(Exception $e)
				{
					error_log("Wow guys, you removed accounts directly from the database.");
					$messages = $e->getCode();
					$messages = explode(' ', $messages);
					if( in_array(Application_Model_APIRequest::ERR_CODE_NO_SUCH_USER, $messages) )
					{
						$redirect_url = 'http://'.$_SERVER['HTTP_HOST'];
						$fb_access_code = $_REQUEST['code'];
						
						error_log( $redirect_url.' '.$fb_access_code );
						self::signupUserFromFB($fb_access_code, $redirect_url);
					}
				}
				return true;
			}
			else
			{
				error_log('User has not authorized us');
				//User hasn't authorized the app
				return false;
			}
		}
		else
		{
			//No active session...user is not logged in to Facebook.
			//So, we don't know her status. Return false
			error_log('User is not logged in Facebook');
			//Turn off auto-login to Facebook
			//header('Location: '.$facebook->getLoginUrl());
			//die();
		}
				
		return false;
	}
	
	//TODO:Token
	public static function signupUserFromFB($fb_access_code, $redirect_url)
	{
		$config = Zend_Registry::get('config');
		
		$app_id = $config->fb->appID;
		$app_secret = $config->fb->appSecretKey;

		$token_url = "https://graph.facebook.com/oauth/access_token?"
		. "client_id=" . $app_id . "&redirect_uri=" . urlencode($redirect_url)
		. "&client_secret=" . $app_secret . "&code=" . $fb_access_code;

		$response = file_get_contents($token_url);
		$params = null;
		parse_str($response, $params);

		$graph_url = "https://graph.facebook.com/me?access_token=".$params['access_token'];
		
		$user = json_decode( file_get_contents($graph_url), true );
		$user['auth_token'] = $params['access_token'];
		error_log( json_encode($user) );
		
		/*
			Try signing in the user...
				if signin successful, 
					check access token, 
					update token if it is not the same.
					redirect to deals or account add
				else
					add user
					redirect to deals or account add
		*/
		try
		{
			$user_obj = new Application_Model_User();
			$user_obj->initWithFbUserId($user['id']);
		}
		catch(Exception $e)
		{
			$messages = $e->getCode();
			$messages = explode(' ', $messages);
			if( in_array(Application_Model_APIRequest::ERR_CODE_NO_SUCH_USER, $messages) )
			{
				$data = $user;
				$response = Application_Model_User::add( $data );
			}
		}
		
		return true;
	}
	
	public static function getCurrentUserDeallrAddress()
	{
		$service_params = array(
			'userId' => Application_Model_User::id()
		);
		$api_request = new Application_Model_APIRequest( array('account', 'deallr'), $service_params );
		$api_response = $api_request->call();
		if (isset($api_response) && isset($api_response['email'][0]) && $api_response['email'][0]) {
			return $api_response['email'][0];
		}
		
		return false;
	}
	
	public static function add($params)
	{
		$auth_session = Zend_Registry::get('auth_session');
		$service_params = array(
			'gender' => $params['gender'],
			'fbFullName' => $params['name'],
			'fbEmailAddress' => $params['email'],
			'fbUserId' => $params['id'],
			'fbLocationName' => isset( $params['location']['name'] ) ? $params['location']['name'] : '',
			'fbLocationId' => isset( $params['location']['id'] ) ? $params['location']['id'] : '',
			'fbAuthToken' => $params['auth_token'],
			'betaToken' => isset($_COOKIE['bic']) && trim($_COOKIE['bic']) ? base64_decode(trim($_COOKIE['bic'])) : ''
		);
		$api_request = new Application_Model_APIRequest( array('user', 'add'), $service_params );
		$api_request->setMethod( Application_Model_APIRequest::METHOD_POST );
		$api_response = $api_request->call();
		if( isset( $api_response ) && $api_response['user'][0]['id'] )
		{
			$auth_session->auth_user = $service_params;
			$auth_session->auth_user['id'] = $api_response['user'][0]['id'];
			$auth_session->auth_user['email'] = $api_response['user'][0]['emailAddress'];
			$auth_session->auth_user['newUser'] = 1;
			$auth_session->auth_user['hasSetupEmailAccounts'] = 0;
			$auth_session->lastAuthCheck = time();
			return $api_response['user'][0];
		}
		
		return array();
	}

	//TODO:			//Capture FB Auth Token And email address	
	public function initWithFbUserId($fbUserId)
	{
        $api_request = new Application_Model_APIRequest( array('login'), array( 'fbUserId' => $fbUserId ) );
		$api_response = $api_request->call();
		$api_response = $api_response['user'][0];
		
		$auth_session = Zend_Registry::get('auth_session');
		
		if( isset( $api_response['id'] ) && trim( $api_response['id'] ) )
		{
			$auth_session->auth_user['id'] = $api_response['id'];
			$auth_session->auth_user['fbUserId'] = $fbUserId;
			$auth_session->auth_user['hasSetupEmailAccounts'] = isset( $api_response['hasSetupEmailAccounts'] ) && $api_response['hasSetupEmailAccounts'] ? 1 : 0;
			$auth_session->auth_user['fbFullName'] = $api_response['name'];
			$this->_id = $api_response['id'];
			$auth_session->lastAuthCheck = time();
		}
		
		return $this;
	}
	
	public static function hasAuthorizedEmailAccounts()
	{
		$auth_session = Zend_Registry::get('auth_session');	
		if ($auth_session && isset($auth_session->auth_user) && isset($auth_session->auth_user['hasSetupEmailAccounts']) ) {
			error_log("hasSetupEmailAccounts = " . ($auth_session->auth_user['hasSetupEmailAccounts'] ? 1 : 0));
			return $auth_session->auth_user['hasSetupEmailAccounts'] == 1 ? 1 : 0;
		}
		
		return 0;
	}

	public static function isNewUser()
	{
		$auth_session = Zend_Registry::get('auth_session');
		if ($auth_session && isset($auth_session->auth_user) && isset($auth_session->auth_user['newUser']) ) {
			return $auth_session->auth_user['newUser'] == 1 ? 1 : 0;
		}
		
		return 0;
	}

	public static function setAuthorizedEmailAccountsFlag()
	{
		$auth_session = Zend_Registry::get('auth_session');	
		$auth_session->auth_user['hasSetupEmailAccounts'] = 1;
	}
		
	public static function id()
	{
		$auth_session = Zend_Registry::get('auth_session');
		return $auth_session->auth_user['id'];
	}
}

