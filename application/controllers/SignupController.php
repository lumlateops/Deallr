<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class SignupController extends DeallrBaseController
{

    public function indexAction()
    {
		$auth_session = Zend_Registry::get('auth_session');
		$redirect_url = 'http://dev.deallr.com/signup/index';
		
		$fbcode = $this->_getParam( 'code' );		
		if(empty($fbcode))
		{
			$betaCodeInCookie = isset($_COOKIE['bic']) && trim($_COOKIE['bic']) ? base64_decode($_COOKIE['bic']) : '';
			
			if (!$betaCodeInCookie) {
				$betacode = $this->_getParam('btoken') ? $this->_getParam('btoken') : '';
				if (!$betacode) {
					$this->messenger->addMessage('Beta invite code missing.');
					header('Location: /');
					die();
				} else {
					$ret_arr = $this->_validateToken($betacode);
					if (!$ret_arr['status']) {
						$this->messenger->addMessage($ret_arr['message']);
						header('Location: /');
						die();
					}
					setcookie('bic', base64_encode($betacode), time()+30*24*60*60, '/', '.deallr.com');
				}
			}
			
			$csrf_state = md5(uniqid(rand(), TRUE)); //CSRF protection
			$auth_session->state = $csrf_state;
			$login_url = Application_Model_Facebook::getFacebookSignupUrl($redirect_url, $csrf_state);
			header('Location: '. $login_url);
			die();
		}
		
		$fbstate = $this->_getParam( 'state' );
		if( isset($auth_session->state) )
		{
			if( $fbstate == $auth_session->state )
			{
				try {
					Application_Model_User::signupUserFromFB( $fbcode, $redirect_url );
				} catch(Exception $e) {
					$this->messenger->addMessage($e->getMessage());
					header('Location: /');
					die();
				}
				
		        $is_authenticated = Application_Model_User::isAuthenticated();
		        if( $is_authenticated )
		        {
					if( Application_Model_User::hasAuthorizedEmailAccounts() )
					{
						$this->_redirector->gotoSimple( '', 'deals', null, array() );
					}
					else
					{
						$this->_redirector->gotoSimple( 'add', 'account', null, array() );
					}
		        }
			}
			else
			{
				echo("The state does not match. You may be a victim of CSRF.");
			}
		}
    }
    
    private function _validateToken($token)
    {
    	$message = "";
    	$status = false;
    	
    	try {
	    	$service_params = array(
	    		'token' => $token
	    	);
			$api_request = new Application_Model_APIRequest( array('token', 'validate'), $service_params );
			$api_response = $api_request->call();
			if (isset($api_response) && isset($api_response['tokenValid'][0]) && $api_response['tokenValid'][0]) {
				$status = true;
			}
		} catch(Exception $e) {
			$message = $e->getMessage();
		}
		
		return array(
			'status' => $status,
			'message' => $message
		);
    }
}

