<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class DealsController extends DeallrBaseController
{
	public function init()
	{
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'json');
        //$ajaxContext->addActionContext('details', 'json');
        $ajaxContext->initContext();
        $this->view->wallet = 0;
		parent::init();
	}
	
    public function indexAction()
    {
        // action body
		$deal_id = 0;
		if ($deal_id = $this->_getParam('deal_id')) {
			$details = Application_Model_Deals::getDetails($deal_id);
			$this->view->page_title = $details['deal_title'];
		} else {
			$this->view->page_title = 'Deals for ' . $this->view->user['fbFullName'];
		}
		
		$page = 1;
		if ($this->_hasParam('page')) {
			$page = $this->_getParam('page');
		}
		
		$sort = null;
		if ($this->_hasParam('sort')) {
			$sort = $this->_getParam('sort');
		}
		
		$page = $page ? $page : 1;
		
		$deals = Application_Model_Deals::getDeals($page, $sort);
		
		$this->view->user_deals = $deals;
		
		$this->view->show_load_more = $deals['max_pages'] > $page;
		
		$this->view->sort_params = Application_Model_Deals::$SORT_PARAMS;
    }
    
    public function detailsAction()
    {
    	$details = array();
    	if ($deal_id = $this->_getParam('deal_id')) {
    		$details = Application_Model_Deals::getDetails($deal_id);
    	}
    	$this->_helper->layout->setLayout('dealdetails');
    	$this->view->details = $details;
    }
}

?>
