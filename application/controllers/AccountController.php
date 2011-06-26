<?php

class AccountController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body        
    }

    public function addAction()
    {
        // action body
        $api_request = new Application_Model_APIRequest( array('providers', 'active') );
		$this->view->providers = $api_request->call();
/*

		$service_params = array(
			"userName" => "test123", 
			"password" => "test123",
			"firstName" => "test", 
			"lastName" => "user", 
			"gender" => "1", 
			"zipCode" => "11111", 
			"fbEmailAddress" => "test@gmail.com", 
			"fbUserId" => "12345"
		);
        $api_request = new Application_Model_APIRequest( array('user', 'add'), $service_params );
        $api_request->setMethod( Application_Model_APIRequest::METHOD_POST );
        $api_request->call();
        $this->view->response = $api_request->response;
        $this->view->request_url = $api_request->request_url;
*/
    }
}



