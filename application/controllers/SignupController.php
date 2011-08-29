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
				Application_Model_User::signupUserFromFB( $fbcode, $redirect_url );
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
}

