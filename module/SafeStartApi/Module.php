<?php

namespace SafeStartApi;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class Module
{
    public $params = array();

    public function init(ModuleManager $moduleManager)
    {
        // config params
        $config = $this->getConfig();
        $this->params = $config['params'];

        // get shared events manager
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();

        // set empty layout on dispatch event
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function ($e) {
            $controller = $e->getTarget();
            $controller->layout('safe-start-api/layout');
        }, 100);

        // handle global error event
        $module = $this;
        $sharedEvents->attach('Zend\Mvc\Application', 'dispatch.error',
            function ($e) use ($module) {
                $module->onDispatchError($e);
            },
            100);

    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function onDispatchError(MvcEvent $e)
    {
        $request = $e->getRequest();
        if ($request instanceof \Zend\Console\Request) return;
        $requestUri = $request->getRequestUri();
        // if api method call need disable layout
        if (substr($requestUri, 0, 5) === '/api/') {
            $viewModel = $e->getViewModel();
            $viewModel->setTerminal(true);
            $serviceManager = $e->getApplication()->getServiceManager();
            if ($e->getParam('exception')) {
                $viewModel->setTemplate('json/500');
                $viewModel->setVariable('exception', $e->getParam('exception'));
                // log exception
                $serviceManager->get('ErrorLogger')->crit($e->getParam('exception'));
            } else {
                $viewModel->setTemplate('json/404');
                // log error
                $serviceManager->get('ErrorLogger')->err('api method not found');
            }
        }
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'RequestLoggerHelper' => 'SafeStartApi\View\Helper\RequestLogger',
            ),
            'factories' => array(
                'getlogResponse' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\RequestLogger();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                }
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConsoleUsage(Console $console){
        return array(
            // Describe available commands
            'ping api [--verbose|-v]' => 'Return current api version',
             array( '--verbose|-v',     '(optional) turn on verbose mode' ),
        );
    }
}
