<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

class CronController extends AbstractActionController
{
    public function indexAction()
    {
        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Check if the user used --verbose or -v flag
        $verbose     = $request->getParam('verbose');

        if (!$verbose){
            return "Test 1";
        }else{
            return "Test 2";
        }

    }

}
