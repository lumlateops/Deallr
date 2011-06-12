<?php

class IndexController extends Zend_Controller_Action
{
	static $users = array(
		'vipul' => 'd32861dda286bd6ff0e3df08b0a15d842b04162b',
		'prachi' => 'fa6933d1c84c32c9a6679bf86ae6096b5ba5eaf9',
		'rrani' => 'a266937bba22d1ac0119477ffa345ad44f1ce678',
		'swasthi' => '08faf07f4d1a4192da554e098a819951637b8db4',
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
				$this->_redirector->gotoSimple('index', 'home', null, array());
				exit("Good logged in");
			}
			else
			{
				$this->view->error = 'Invalid username/password combination';
			}
		}
    }
}

