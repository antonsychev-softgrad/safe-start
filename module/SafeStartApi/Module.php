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

/**
 * Class Module
 * @package SafeStartApi
 */
class Module
{
    /**
     * @var array
     */
    public $params = array();

    /**
     * @param ModuleManager $moduleManager
     */
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

        register_shutdown_function(function () use ($module)
        {
            if ($e = error_get_last()) {
                $module->onCodeExecutionError($e);
            }
        } );
    }

    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    /**
     * @param MvcEvent $e
     */
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
                switch (get_class($e->getParam('exception'))) {
                    case 'SafeStartApi\Base\Exception\Rest401':
                        $viewModel->setTemplate('json/response');
                        $viewModel->setVariable('answer', array(
                            'errorMessage' => 'Access denied: Authorization required.',
                        ));
                        $viewModel->setVariable('errorCode', 401);
                        $viewModel->setVariable('statusCode', 401);
                        $e->getResponse()->setStatusCode(401);
                        break;
                    case 'SafeStartApi\Base\Exception\Rest403':
                        $viewModel->setTemplate('json/response');
                        $viewModel->setVariable('answer', array(
                            'errorMessage' => 'Access denied: ' . $e->getParam('exception')->getMessage(),
                        ));
                        $viewModel->setVariable('errorCode', 403);
                        $viewModel->setVariable('statusCode', 403);
                        $e->getResponse()->setStatusCode(201);
                        break;
                    default:
                        $viewModel->setTemplate('json/500');
                        $viewModel->setVariable('exception', $e->getParam('exception'));
                        break;
                }
                // log exception
                $serviceManager->get('ErrorLogger')->crit($e->getParam('exception'));
            } else {
                $viewModel->setTemplate('json/404');
                // log error
                $serviceManager->get('ErrorLogger')->err('api method not found');
            }
        }
    }

    public function onCodeExecutionError($e)
    {
        $logger = new \Zend\Log\Logger;
        $dir = __DIR__ . '/../../';
        if (!is_dir($dir . 'data/logs')) {
            if (mkdir($dir . 'data/logs', 0777)) {

            }
        }
        if (!is_dir($dir . 'data/logs/errors')) {
            if (mkdir($dir . 'data/logs/errors', 0777)) {
            }
        }
        $writer = new \Zend\Log\Writer\Stream($dir . 'data/logs/errors/' . date('Y-m-d') . '.log');
        $logger->addWriter($writer);

        $logger->debug(json_encode($e));
    }

    /**
     * @return array
     */
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

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
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

    /**
     * @param Console $console
     * @return array
     */
    public function getConsoleUsage(Console $console)
    {
        return array(
            // Describe available commands
            'ping api [--verbose|-v]' => 'Return current api version',
            'doctrine set-def-data [--verbose|-v]' => 'Update database with fixtures data',
            array('--verbose|-v', '(optional) turn on verbose mode'),
            'resque start [--verbose|-v]' => 'Update database with fixtures data',
            array('--verbose|-v', '(optional) turn on verbose mode'),
        );
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
                            $class = isset($session['config']['class']) ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
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
                'RequestLogger' => function ($sm) {
                    $logger = new \SafeStartApi\Base\Logger;
                    if (!is_dir('./data/logs')) {
                        if (mkdir('./data/logs', 0777)) {

                        }
                    }
                    if (!is_dir('./data/logs/requests')) {
                        if (mkdir('./data/logs/requests', 0777)) {
                        }
                    }
                    $writer = new \Zend\Log\Writer\Stream('./data/logs/requests/' . date('Y-m-d') . '.log');
                    $logger->addWriter($writer);
                    return $logger;
                },
                'ResqueLogger' => function ($sm) {
                        $logger = new \SafeStartApi\Base\Logger;
                        if (!is_dir('./data/logs')) {
                            if (mkdir('./data/logs', 0777)) {

                            }
                        }
                        if (!is_dir('./data/logs/resque')) {
                            if (mkdir('./data/logs/resque', 0777)) {
                            }
                        }
                        $writer = new \Zend\Log\Writer\Stream('./data/logs/resque/' . date('Y-m-d') . '.log');
                        $logger->addWriter($writer);
                        return $logger;
                    },
                'ErrorLogger' => function ($sm) {
                    $logger = new \Zend\Log\Logger;
                    if (!is_dir('./data/logs/')) {
                        if (mkdir('./data/logs/', 0777)) {
                            // todo: handle exception
                        }
                    }
                    if (!is_dir('./data/logs/errors/')) {
                        if (mkdir('./data/logs/errors/', 0777)) {

                        }
                    }
                    $writer = new \Zend\Log\Writer\Stream('./data/logs/errors/' . date('Y-m-d') . '.log');
                    $logger->addWriter($writer);
                    return $logger;
                },
                'mail.transport' => function ($sm) {
                    $config = $sm->get('config');
                    $transport = new \Zend\Mail\Transport\Smtp();
                    $transport->setOptions(new \Zend\Mail\Transport\SmtpOptions($config['mail']['transport']['options']));

                    return $transport;
                },
            ),
        );
    }
}
