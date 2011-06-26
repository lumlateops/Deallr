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
        // action body
        if( $signed_request = $this->_getParam( 'signed_request' ) )
        {
        	$parser = new Application_Model_FacebookResponseParser( $signed_request );
        	$data = $parser->parse();
        	
        	//Synchronous Call - Add user
			$response = Application_Model_User::add( $data['registration'] );
        	if( isset( $response ) && $response['user']['id'] ) //Check success
        	{
        		$this->_redirector->gotoSimple( 'add', 'account', null, array() );
				exit("Good logged in");
        	}
        }
    }
}

