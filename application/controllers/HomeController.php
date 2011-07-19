<?php

class HomeController extends Zend_Controller_Action
{

	protected $_redirector = null;

    public function init()
    {
		if( !isset( $_SESSION["user"] ) )
		{
			$this->_redirector = $this->_helper->getHelper('Redirector');
			$this->_redirector->gotoUrl('/index');
			exit();
		}
/*
        $auth_session = Zend_Registry::get('auth_session');
        $this->view->user = $auth_session->auth_user;		
*/
    }

    public function indexAction()
    {
        // action body
		include_once(APPLICATION_PATH.'/data/Deals.php');
		$this->view->user_deals = Application_Data_Deals::getParsedDeals();
    }


}

