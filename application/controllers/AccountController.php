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
    }
}



