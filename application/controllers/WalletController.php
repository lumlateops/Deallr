<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class WalletController extends DeallrBaseController
{
    public function indexAction()
    {
        // action body
		include_once(APPLICATION_PATH.'/data/Deals.php');
		$this->view->user_deals = Application_Data_Deals::getParsedDeals();
    }
}

?>