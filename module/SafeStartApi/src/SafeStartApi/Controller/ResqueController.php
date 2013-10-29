<?php

namespace SafeStartApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\MvcEvent;
use Zend\Console\Console;
use Zend\Console\Prompt;

class ResqueController extends AbstractActionController
{
    protected $console;
    protected $logger;

    private $QUEUES = 'default,new_checklist_uploaded,new_email_checklist_uploaded';


    public function onDispatch(MvcEvent $e)
    {
        $request = $this->getRequest();
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }
        $this->logger = $this->getServiceLocator()->get('ResqueLogger');
        $this->console = Console::getInstance();

        parent::onDispatch($e);
    }

    public function startAction()
    {
        $APP_PATH = __DIR__ . '/../../../../../';

        $request = $this->getRequest();
        // Check if the user used --verbose or -v flag
        $verbose = $request->getParam('verbose');
        $command = array();
       // $command[] = 'nohup sudo -u www-data QUEUE=' . $this->QUEUES;
        $command[] = 'QUEUE=' . $this->QUEUES;
        if ($verbose) $command[] = 'VVERBOSE=1';
        $command[] = 'APP_INCLUDE=' . $APP_PATH . 'module/SafeStartApi/jobs_classes_autoloader.php';
        $command[] = 'php ' . $APP_PATH . 'vendor/chrisboulton/php-resque/resque.php';
        $command = implode(' ', $command);
        $this->console->write($command . "\r\n");
        $this->logger->info($command);
        return $this->executeShelCommand($command);
    }

    private function executeShelCommand($command)
    {
        if (function_exists('system')) {
            system($command, $return_var);
        } else if (function_exists('passthru')) {
            passthru($command, $return_var);
        } else if (function_exists('exec')) {
            exec($command, $output, $return_var);
            $output = implode("n", $output);
            $return_var = 0;
        } else if (function_exists('shell_exec')) {
            $output = shell_exec($command);
            $return_var = 0;
        } else {
            $output = 'Command execution not possible on this system';
            $return_var = 0;
        }

        while ($return_var) {
            ob_start();
            $output = ob_get_contents();
            ob_end_clean();
            $this->console->write($output);
            sleep(5);
        }

        return $output;

    }

}
