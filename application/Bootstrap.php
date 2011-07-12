<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initConfig()
	{
	    $config = new Zend_Config($this->getOptions(), true);
	    Zend_Registry::set('config', $config);
	    return $config;
	}

	protected function _initRewrite()
	{
	    $front = Zend_Controller_Front::getInstance();
	    $router = $front->getRouter();
	    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'production');
	    $router->addConfig($config,'routes');
	}
		
	public function run()
	{
		$auth_session = new Zend_Session_Namespace('Auth');
		Zend_Registry::set('auth_session', $auth_session);
		parent::run();
	}
}

