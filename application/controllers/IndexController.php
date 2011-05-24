<?php

class IndexController extends Zend_Controller_Action
{
	static $users = array(
	);
	
	const SALT = 'maRd9x50xQLs6uDM-';
	protected $_redirector = null;

    public function init()
    {
        /* Initialize action controller here */
		$this->_helper->layout()->setLayout('login');
		$this->_redirector = $this->_helper->getHelper('Redirector');
    }

    public function indexAction()
    {
        // action body
		$username = $this->_getParam('u');
		if( trim( $username ) )
		{
			$password = trim($this->_getParam('p'));
			if( isset( self::$users[$username] ) && self::$users[$username] == sha1(self::SALT.$password) )
			{
				$_SESSION["user"] = base64_encode( $username );
				$this->_redirector->gotoSimple('index', 'u', null, array('u'=>$username));
				exit("Good logged in");
			}
			else
			{
				$this->view->error = 'Invalid username/password combination';
			}
		}
    }
}

