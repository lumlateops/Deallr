<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class IndexController extends DeallrBaseController
{
    public function indexAction()
    {
		$this->_helper->layout->setLayout('landing');
		if( $this->is_authenticated )
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
    
    public function tosAction()
    {
    }

    public function privacyAction()
    {
    }

    public function aboutAction()
    {
    }

    public function contactAction()
    {
    }
}

