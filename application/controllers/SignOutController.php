<?php

require_once APPLICATION_PATH . '/controllers/DeallrBaseController.php';

class SignOutController extends DeallrBaseController
{
    public function indexAction()
    {
        Application_Model_User::logOut();
		$this->_redirector->gotoSimple( '', 'landing', null, array() );
    }
}

?>
