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
                    'get-vehicle-info' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/vehicle/:id/getinfo',
                            'constraints' => array(
                                'id' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Vehicle',
                                'action' => 'getdatabyid',
                            ),
                        ),
                    ),
                    'get-vehicle-checklist' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/vehicle/:id/getchecklist',
                            'constraints' => array(
                                'id' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Vehicle',
                                'action' => 'getchecklist',
                            ),
                        ),
                    ),
                    'complete-vehicle-checklist' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/vehicle/:id/completechecklist',
                            'constraints' => array(
                                'id' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Vehicle',
                                'action' => 'completechecklist',
                            ),
                        ),
                    ),
                    'update-user-profile' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/user/:id/profile/update',
                            'constraints' => array(
                                'id' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'UserProfile',
                                'action' => 'update',
                            ),
                        ),
                    ),
                    'update-company-subscription' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/admin/company/:id/update',
                            'constraints' => array(
                                'id' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin',
                                'action' => 'updateCompany',
                            ),
                        ),
                    ),
                    'delete-company-subscription' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/admin/company/:id/delete',
                            'constraints' => array(
                                'id' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin',
                                'action' => 'deleteCompany',
                            ),
                        ),
                    ),
                    'credentials-company-subscription' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/admin/company/:id/send-credentials',
                            'constraints' => array(
                                'id' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Admin',
                                'action' => 'sendCredentials',
                            ),
                        ),
                    ),
                    'upload-images' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/upload-images',
                            'defaults' => array(
                                'controller' => 'ProcessData',
                                'action' => 'uploadImages',
                            ),
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
            'SafeStartApi\Controller\Doctrine' => 'SafeStartApi\Controller\DoctrineController',
            'SafeStartApi\Controller\UserProfile' => 'SafeStartApi\Controller\UserProfileController',
            'SafeStartApi\Controller\Admin' => 'SafeStartApi\Controller\AdminController',
            'SafeStartApi\Controller\Company' => 'SafeStartApi\Controller\CompanyController',
            'SafeStartApi\Controller\ProcessData' => 'SafeStartApi\Controller\ProcessDataController',
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
            'default' => ''
        ),
        'text' =>  array(
            'id' => 2,
            'default' => ''
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
        'photo' => array(
            'id' => 4,
            'default' => ''
        ),
        'coordinates' =>array(
            'id' => 5,
            'default' => ''
        ),
        'group' => array(
            'id' => 6,
        ),
        'datePicker' => array(
            'id' => 7,
            'default' => ''
        )
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
);
