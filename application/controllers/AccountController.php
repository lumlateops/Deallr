<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class AccountController extends DeallrBaseController
{

    public function init()
    {
    	parent::init();
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add', 'json');
        $ajaxContext->initContext();
    }

    public function indexAction()
    {
		$this->_redirector->gotoSimple('add', 'account', null, array());
        // action body
    }

    public function addAction()
    {
    	$provider = $this->_getParam('provider');
    	error_log(' Provider = '.$provider);
    	$this->view->err_msg = '';
    	
    	if ($provider) {
    		try {
    			$account_obj = new Application_Model_Account($provider);
    			$provider_url = $account_obj->add();
    			if ($provider_url) {
    				header('Location: ' . $provider_url);
    				die();
    			}
    		}
    		catch(Exception $e) {
    			$this->view->err_msg = $e->getMessage();
    		}
    	}
    	
    	// action body
        $api_request = new Application_Model_APIRequest( array('providers', 'active') );
		$response = $api_request->call();
		$this->view->providers = $response['providers'];
    }
    
    public function upgradeAction()
    {
    	$userId = $this->_getParam('userId');
    	$provider = $this->_getParam('provider');
    	$oauth_verifier = $this->_getParam('oauth_verifier');
    	$oauth_token = $this->_getParam('oauth_token');
    	
    	//Validate UserId
		$account_obj = new Application_Model_Account($provider);
		$this->view->status = $account_obj->upgradeToken($oauth_verifier, $oauth_token);
		$email = '';
		if ($this->view->status) {
			$this->_redirector->gotoSimple('thankyou', 'account', null, array('email' => $email));
		}
		else {
			$this->_redirector->gotoSimple('add', 'account', null, array());
		}    	
    }
    
    public function thankyouAction()
    {
    	$email = $this->_getParam('email');
    	if (!$email) {
    		$this->_redirector->gotoSimple('add', 'account', null, array());
    	}
    	else {
    		$this->view->email = $this->_getParam('email');
    	}
    }
}



