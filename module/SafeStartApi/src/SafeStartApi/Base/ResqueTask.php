<?php

namespace SafeStartApi\Base;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class ResqueTask
{
    protected function executeShelCommand($command)
    {
        $APP_PATH = __DIR__ . '/../../../../../';
        $command = 'php ' . $APP_PATH . 'public/index.php ' . $command;
        $output = null;

        if (function_exists('system')) {
            system($command, $return_var);
        } else if (function_exists('passthru')) {
            passthru($command, $return_var);
        } else if (function_exists('exec')) {
            exec($command, $output, $return_var);
            $output = implode("n", $output);
        } else if (function_exists('shell_exec')) {
            $output = shell_exec($command);
        }
        return $output;
    }

}
