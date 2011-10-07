<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class WalletController extends DeallrBaseController
{
	public function init()
	{
        // action body
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'json');
        $ajaxContext->addActionContext('add', 'json');
        $ajaxContext->addActionContext('remove', 'json');
        $ajaxContext->initContext();
        
        $this->view->wallet = 1;
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
			$this->view->page_title = 'Deals in ' . $this->view->user['fbFullName'] . '\'s Wallet';
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
		
		$deals = Application_Model_Deals::getWalletDeals($page, $sort);
		
		$this->view->user_deals = $deals;
		
		$this->view->show_load_more = $deals['max_pages'] > $page;
		
		$this->view->sort_params = Application_Model_Deals::$SORT_PARAMS;
    }
    
    public function addAction()
    {
    	$status = false;
    	if ($deal_id = $this->_getParam('deal_id')) {
    		$status = Application_Model_Deals::addToWallet($deal_id);
    	}
    	$this->view->status = $status;
    }
    
    public function removeAction()
    {
    	$status = false;
    	if ($deal_id = $this->_getParam('deal_id')) {
    		$status = Application_Model_Deals::removeFromWallet($deal_id);
    	}
    	$this->view->status = $status;    
    }
}

?>