<?php

class IndexController extends Zend_Controller_Action
{
	static $users = array(
		'vipul' => '24824d7dbfa63ebcbed9fbb17d49c988dd2714a8',
		'prachi' => '3f61f0ff5daf1be35ed5d83174d858187554035c',
		'rrani' => '4af96a592299b4792aae3187bc0aa8cdd9c0879a',
		'swasthi' => '4b85fec39f6d955ebe468d3349f012afeab53852',
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

