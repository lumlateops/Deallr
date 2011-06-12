<?php

class UController extends Zend_Controller_Action
{

	protected $_redirector = null;

    public function init()
    {
		$this->_redirector = $this->_helper->getHelper('Redirector');
		if( !isset( $_SESSION["user"] ) )
		{
			$this->_redirector->gotoUrl('/index');
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

