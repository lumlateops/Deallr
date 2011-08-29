<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class SigninController extends DeallrBaseController
{

    public function init()
    {
    	parent::init();
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
				$this->view->url = $user_obj->hasAuthorizedEmailAccounts() ? '/deals' : '/account/add';
			}
			catch( Exception $e )
			{
				$this->view->url = '';
			}
        }
    }
}
