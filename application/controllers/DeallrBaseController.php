<?php

class DeallrBaseController extends Zend_Controller_Action
{
	protected $_redirector = null;
	protected $is_authenticated = null;

    public function init()
    {
    	$config = Zend_Registry::get('config');
    	$this->view->ga_id = $config->ga->id;
    	
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$this->view->controller = $request->getControllerName();
		$this->view->action = $request->getActionName();
		    
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->is_authenticated = Application_Model_User::isAuthenticated();

        if (!$this->is_authenticated && !in_array($this->view->controller, array('index', 'signup')))
        {
        	if ($this->view->controller == 'deals' && in_array($this->view->action, array('deal', 'details'))) {
        	
        	} else {
        		$this->_redirector->gotoSimple('', '', null, array());
        		exit();
        	}
        }
		
		if ($this->is_authenticated) {
			$auth_session = Zend_Registry::get('auth_session');
       		$this->view->user = $auth_session->auth_user;
        }
	}
}

?>