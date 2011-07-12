<?php

class SignupController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_redirector = $this->_helper->getHelper('Redirector');
    }

    public function indexAction()
    {
		$auth_session = Zend_Registry::get('auth_session');
/*
        $router = $this->getFrontController()->getRouter();
        $redirect_url = $router->assemble(array('controller' => 'signup', 'action' => 'index'), 'default', true);
*/
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
						$this->_redirector->gotoSimple( '', 'home', null, array() );
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

