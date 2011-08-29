<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class UController extends DeallrBaseController
{

	protected $_redirector = null;

    public function init()
    {
    	parent::init();
    	
		$this->_redirector = $this->_helper->getHelper('Redirector');
		if( !isset( $_SESSION["user"] ) )
		{
			$this->_redirector->gotoUrl('/landing');
			exit();
		}
    }

    public function indexAction()
    {
        // action body
		include_once(APPLICATION_PATH.'/data/Deals.php');
		$this->view->user_deals = Application_Data_Deals::getDeals();
    }


}

