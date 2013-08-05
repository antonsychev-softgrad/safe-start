<?php

return array(
    'params' => array(
        'version' => '0.1',
        'output' => 'json',
        'href' => '/api/',
    ),
    'router' => array(
        'routes' => array(
            'api' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api',
                    'defaults' => array(
                        '__NAMESPACE__' => 'SafeStartApi\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
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
            'RequestLogger' => function ($sm) {
                $logger = new \Zend\Log\Logger;
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

                    $sessionManager = new \Zend\Session\SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

                    if (isset($session['validators'])) {
                        $chain = $sessionManager->getValidatorChain();
                        foreach ($session['validators'] as $validator) {
                            $validator = new $validator();
                            $chain->attach('session.validate', array($validator, 'isValid'));

                        }
                    }
                } else {
                    $sessionManager = new \Zend\Session\SessionManager();
                }
                \Zend\Session\Container::setDefaultManager($sessionManager);
                return $sessionManager;
            },
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'SafeStartApi\Controller\Index' => 'SafeStartApi\Controller\IndexController',
            'SafeStartApi\Controller\Docs' => 'SafeStartApi\Controller\DocsController',
            'SafeStartApi\Controller\Cron' => 'SafeStartApi\Controller\CronController',
            'SafeStartApi\Controller\User' => 'SafeStartApi\Controller\UserController',
            'SafeStartApi\Controller\WebPanel' => 'SafeStartApi\Controller\WebPanelController',
            'SafeStartApi\Controller\Vehicle' => 'SafeStartApi\Controller\VehicleController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'template_map' => array(
            'ajax/layout' => __DIR__ . '/../view/ajax/layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'ping-api' => array(
                    'options' => array(
                        'route' => 'ping api [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'SafeStartApi\Controller\CronController',
                            'action' => 'index'
                        )
                    )
                ),
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'application_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/SafeStartApi/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'SafeStartApi\Entity' => 'application_entities'
                )
            )
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'SafeStartApi\Entity\User',
                'identity_property' => 'username',
                'credential_property' => 'password',
                'credential_callable' => 'SafeStartApi\Entity\User::verifyPassword'
            ),
        )
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                'SafeStartApi' => __DIR__ . '/../public',
            ),
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'AnswerPlugin' => 'SafeStartApi\Controller\Plugin\AnswerPlugin',
            'AclPlugin' => 'SafeStartApi\Controller\Plugin\AclPlugin',
        )
    ),
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'SafeStartApi',
                'remember_me_seconds' => 360,
              //  'use_cookies' => true,
                'save_path' =>  __DIR__ . '/../../../data/sessions',
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
           // 'Zend\Session\Validator\RemoteAddr',
          //  'Zend\Session\Validator\HttpUserAgent',
        ),
    ),
);
