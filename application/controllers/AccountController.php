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
    }

    public function indexAction()
    {
		$redirector = $this->_helper->getHelper('Redirector');
		$redirector->gotoSimple('add', 'account', null, array());
        // action body
    }

    public function addAction()
    {
    	$email =  $this->_getParam('email');
    	$provider = $this->_getParam('provider');
    	
    	error_log("Email ".$email.' Provider = '.$provider);
    	if( $email && $provider )
    	{
    		$account_obj = new Application_Model_Account($email, $provider);
    		$this->view->provider_url = $account_obj->add();
    	}
    	else
    	{
	        $is_authenticated = Application_Model_User::isAuthenticated();
	        if( !$is_authenticated )
	        {
	        	$this->_redirector->gotoSimple( '', 'landing', null, array() );
	        }
	    	$auth_session = Zend_Registry::get('auth_session');
	    	
	        // action body
	        $api_request = new Application_Model_APIRequest( array('providers', 'active') );
			$response = $api_request->call();
			$this->view->providers = $response['providers'];
			$this->view->user = $auth_session->auth_user;
		}	
    }
    
    public function upgradeAction()
    {
    	$queryString = $this->_getParam('queryString');
    	error_log('Query String = '.$queryString);	
    }
}



