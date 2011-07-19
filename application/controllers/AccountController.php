<?php

class AccountController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add', 'json');
        $ajaxContext->initContext();
        $auth_session = Zend_Registry::get('auth_session');
        $this->view->user = $auth_session->auth_user;        
    }

    public function indexAction()
    {
		$this->_redirector->gotoSimple('add', 'account', null, array());
        // action body
    }

    public function addAction()
    {
    	$email =  $this->_getParam('email');
    	$provider = $this->_getParam('provider');
    	
    	error_log("Email ".$email.' Provider = '.$provider);
    	if( $email && $provider )
    	{
    		$this->view->err = '';
    		try
    		{
    			$account_obj = new Application_Model_Account($email, $provider);
    			$this->view->provider_url = $account_obj->add();
    		}
    		catch( Exception $e )
    		{
    			$this->view->err = $e->getMessage();
    		}
    	}
    	else
    	{
	        $is_authenticated = Application_Model_User::isAuthenticated();
	        if( !$is_authenticated )
	        {
	        	$this->_redirector->gotoSimple( '', 'landing', null, array() );
	        }
	    	
	        // action body
	        $api_request = new Application_Model_APIRequest( array('providers', 'active') );
			$response = $api_request->call();
			$this->view->providers = $response['providers'];
		}
    }
    
    public function upgradeAction()
    {
    	$userId = $this->_getParam('userId');
    	$email = $this->_getParam('email');
    	$provider = $this->_getParam('provider');
    	$oauth_verifier = $this->_getParam('oauth_verifier');
    	$oauth_token = $this->_getParam('oauth_token');
    	
    	//Validate UserId
		$account_obj = new Application_Model_Account($email, $provider);
		$this->view->status = $account_obj->upgradeToken($oauth_verifier, $oauth_token);
		if( $this->view->status )
		{
			$this->_redirector->gotoSimple('thankyou', 'account', null, array('email' => $email));
		}
		else
		{
			$this->_redirector->gotoSimple('add', 'account', null, array());
		}    	
    }
    
    public function thankyouAction()
    {
    	$email = $this->_getParam('email');
    	if( !$email )
    	{
    		$this->_redirector->gotoSimple('add', 'account', null, array());
    	}
    	else
    	{
    		$this->view->email = $this->_getParam('email');
    	}
    }
}



