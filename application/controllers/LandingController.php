<?php

class LandingController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_redirector = $this->_helper->getHelper('Redirector');
    }

    public function indexAction()
    {
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
}

