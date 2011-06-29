<?php

class AccountController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$redirector = $this->_helper->getHelper('Redirector');
		$redirector->gotoSimple('add', 'account', null, array());
        // action body
    }

    public function addAction()
    {
        // action body
        $api_request = new Application_Model_APIRequest( array('providers', 'active') );
		$response = $api_request->call();
		$this->view->providers = $response['providers'];
    }
}



