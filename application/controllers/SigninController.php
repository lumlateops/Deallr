<?php

class SigninController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'json');
        $ajaxContext->initContext();
    }

    public function indexAction()
    {
    	$id = 0;
        if( $this->getRequest()->isPost() && $this->_hasParam('uid') && $id = $this->_getParam('uid') )
        {
        	try
        	{
        		$user_obj = new Application_Model_User();
        		$user_obj->initWithFbUserId($id);
				$this->view->url = $user_obj->hasAuthorizedEmailAccounts() ? '/home' : '/account/add';
			}
			catch( Exception $e )
			{
				$this->view->url = '';
			}
        }
    }
}
