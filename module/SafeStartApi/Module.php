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
        //$this->bootstrapSession($e);
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

    public function bootstrapSession($e)
    {
        $session = $e->getApplication()
            ->getServiceManager()
            ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');
        if (!isset($container->init)) {
            $session->regenerateId(true);
            $container->init = 1;
        }
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');
                    if (isset($config['session'])) {
                        $session = $config['session'];

                        $sessionConfig = null;
                        if (isset($session['config'])) {
                            $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                            $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                            $sessionConfig = new $class();
                            $sessionConfig->setOptions($options);
                        }

                        $sessionStorage = null;
                        if (isset($session['storage'])) {
                            $class = $session['storage'];
                            $sessionStorage = new $class();
                        }

                        $sessionSaveHandler = null;
                        if (isset($session['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session['save_handler']);
                        }

                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

                        if (isset($session['validators'])) {
                            $chain = $sessionManager->getValidatorChain();
                            foreach ($session['validators'] as $validator) {
                                $validator = new $validator();
                                $chain->attach('session.validate', array($validator, 'isValid'));

                            }
                        }
                    } else {
                        $sessionManager = new SessionManager();
                    }
                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },
            ),
        );
    }
}
