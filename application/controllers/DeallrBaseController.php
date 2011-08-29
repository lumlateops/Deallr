<?php

class DeallrBaseController extends Zend_Controller_Action
{
	protected $_redirector = null;
	protected $is_authenticated = null;

    public function init()
    {
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$this->view->controller = $request->getControllerName();
    
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->is_authenticated = Application_Model_User::isAuthenticated();

        if( !$this->is_authenticated && !in_array($this->view->controller, array('landing', 'index', 'signup')) )
        {
        	error_log("Redirecting");
        	$this->_redirector->gotoSimple('', 'landing', null, array());
        	exit();
        }
		
		$auth_session = Zend_Registry::get('auth_session');
        $this->view->user = $auth_session->auth_user;
	}
}

?>