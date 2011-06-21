<?php

class LandingController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $config = Zend_Registry::get('config');
        $this->view->fb_app_id = $config->fb->appID;
    }
}

