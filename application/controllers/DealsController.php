<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class DealsController extends DeallrBaseController
{
    public function indexAction()
    {
        // action body
		include_once(APPLICATION_PATH.'/data/Deals.php');
		$deal_id = 0;
		if($deal_id = $this->_getParam('deal_id')) {
			$this->view->page_title = Application_Data_Deals::getTitle($deal_id);
		}
		$this->view->user_deals = Application_Data_Deals::getParsedDeals();
		$this->view->unread_deals_count = Application_Data_Deals::getUnreadDealsCount();
    }
}

?>
