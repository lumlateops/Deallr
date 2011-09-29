<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class DealsController extends DeallrBaseController
{
    public function indexAction()
    {
        // action body
		$deal_id = 0;
		if($deal_id = $this->_getParam('deal_id')) {
			$this->view->page_title = Application_Data_Deals::getTitle($deal_id);
		} else {
			$this->view->page_title = 'Deals for ' . $this->view->user['fbFullName'];
		}
		
		$deals = Application_Model_Deals::getDeals();
		$this->view->user_deals = $deals['deals'];
    }
}

?>
