<?php

require_once('routes.config.php');

return array(
    'params' => array(
        'version' => '0.1',
        'output' => 'json',
        'href' => '/api/',
    ),
    'router' => $routes,
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
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
            'SafeStartApi\Controller\Vehicle' => 'SafeStartApi\Controller\PublicVehicleController',
            'SafeStartApi\Controller\Doctrine' => 'SafeStartApi\Controller\DoctrineController',
            'SafeStartApi\Controller\UserProfile' => 'SafeStartApi\Controller\UserProfileController',
            'SafeStartApi\Controller\Admin' => 'SafeStartApi\Controller\AdminController',
            'SafeStartApi\Controller\Company' => 'SafeStartApi\Controller\CompanyController',
            'SafeStartApi\Controller\ProcessData' => 'SafeStartApi\Controller\ProcessDataController',
            'SafeStartApi\Controller\Info' => 'SafeStartApi\Controller\InfoController',
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
                            'controller' => 'SafeStartApi\Controller\Cron',
                            'action' => 'index'
                        )
                    )
                ),
                'set-def-bd-data' => array(
                    'options' => array(
                        'route' => 'doctrine set-def-data [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'SafeStartApi\Controller\Doctrine',
                            'action' => 'setDefData'
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
            'MailPlugin' => 'SafeStartApi\Controller\Plugin\MailPlugin',
            'ValidationPlugin' => 'SafeStartApi\Controller\Plugin\ValidationPlugin',
            'UploadPlugin' => 'SafeStartApi\Controller\Plugin\UploadPlugin',
            'PdfPlugin' => 'SafeStartApi\Controller\Plugin\PdfPlugin',
            'GetDataPlugin' => 'SafeStartApi\Controller\Plugin\GetDataPlugin',
            'PushNotificationPlugin' => 'SafeStartApi\Controller\Plugin\PushNotificationPlugin',
        )
    ),
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'SafeStartApi',
                'remember_me_seconds' => 3600,
                //  'use_cookies' => true,
                'save_path' => __DIR__ . '/../../../data/sessions',
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            'Zend\Session\Validator\RemoteAddr',
            //  'Zend\Session\Validator\HttpUserAgent',
        ),
    ),
    'fieldTypes' => array(
        'radio' => array(
            'id' => 1,
            'options' => array(
                array(
                    'value' => 'Yes',
                    'label' => 'Yes'
                ),
                array(
                    'value' => 'No',
                    'label' => 'No'
                ),
                array(
                    'value' => 'N/A',
                    'label' => 'N/A'
                ),
            ),
            'default' => 'N/A'
        ),
        'checkbox' => array(
            'id' => 3,
            'options' => array(
                array(
                    'value' => 'Yes',
                    'label' => 'Yes'
                ),
                array(
                    'value' => 'No',
                    'label' => 'No'
                ),
            ),
            'default' => ''
        ),
        'text' => array(
            'id' => 2,
            'default' => ''
        ),
        'photo' => array(
            'id' => 4,
            'default' => ''
        ),
        'coordinates' => array(
            'id' => 5,
            'default' => ''
        ),
        'datePicker' => array(
            'id' => 7,
            'default' => ''
        ),
        'group' => array(
            'id' => 6,
        ),
    ),
    'mail' => array(
        'from' => 'admin@safe-start.dev',
        'transport' => array(
            'options' => array(
                'host' => 'smtp.gmail.com',
                'connection_class' => 'plain',
                'connection_config' => array(
                    'username' => 'test21141@gmail.com',
                    'password' => 'test211411',
                    'ssl' => 'tls'
                ),
            ),
        ),
    ),
    'defUsersPath' => '/data/users/',
    'pdf' => array(
        'name' => 'checklist_review',
        'ext' => '.pdf', // automatic add to name
        'template_for_name' => array(
            'format' => "{%s}", // only
            'template' => "{name}_{user}_{vehicle}_{checkList}_at_{date}", // available: {name}, {user}, {vehicle}, {checkList}, {date}
        ),
    ),
    'developerApi' => array(
        'google' => array(
            'key' => 'AIzaSyDE7B2A5PvGmkFTgdVX21Al-mabPx1uB0E'
        ),
        'apple' => array(
            'key' =>  __DIR__ . '/apple_push_key.pem',
            'password' =>  '',
        )
    ),
);
