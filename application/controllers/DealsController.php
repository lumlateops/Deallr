<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class DealsController extends DeallrBaseController
{
	public function init()
	{
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'json');
        $ajaxContext->addActionContext('got-any', 'json');
        $ajaxContext->initContext();
        $this->view->wallet = 0;
		parent::init();
	}
	
    public function indexAction()
    {
        // action body
		$this->view->page_title = 'Deals for ' . $this->view->user['fbFullName'];
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
		
		$this->view->deallr_address = Application_Model_User::getCurrentUserDeallrAddress();
		
		$this->view->fetch_status = $deals['fetch_status'];
    }
    
    public function detailsAction()
    {
    	$is_public = $this->_hasParam('s') ? $this->_getParam('s') : false;
    	$details = array();
    	if ($deal_id = $this->_getParam('deal_id')) {
    		$details = Application_Model_Deals::getDetails($deal_id, $is_public);
    	}
    	$this->_helper->layout->setLayout('dealdetails');
    	$this->view->details = $details;
    }
    
    public function dealAction()
    {
		$deal_share_handle = '';
		$deal_share_handle_separator = '/';
		
		if ($this->_getParam('deal_year') 
			&& $this->_getParam('deal_month') 
			&& $this->_getParam('deal_day') 
			&& $this->_getParam('deal_share_handle')) {
			
			$deal_share_handle = $this->_getParam('deal_year')
								 . $deal_share_handle_separator
								 . $this->_getParam('deal_month')
								 . $deal_share_handle_separator
								 . $this->_getParam('deal_day')
								 . $deal_share_handle_separator
								 . $this->_getParam('deal_share_handle');
			
			$details = Application_Model_Deals::getDealDetailByShareURLHandle($deal_share_handle);
			$this->view->page_title = $details['deal_title'];
			$this->view->page_desc = $details['deal_details'];
			$this->view->deal_details = $details;
			$this->view->deal_id = $details['deal_id'];
		}
    }
    
    public function gotAnyAction()
    {
		$deals = Application_Model_Deals::getDeals();
		$this->view->status = (int)$deals['deal_count'] > 0 ? 1 : 0;
    }
}

?>
