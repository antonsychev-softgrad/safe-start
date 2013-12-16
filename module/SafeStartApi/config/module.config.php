<?php

require('routes.config.php');
require('api.config.php');

$general = array(
    'router' => array(
        'routes' => $routes
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
            'SafeStartApi\Controller\PublicVehicle' => 'SafeStartApi\Controller\PublicVehicleController',
            'SafeStartApi\Controller\Doctrine' => 'SafeStartApi\Controller\DoctrineController',
            'SafeStartApi\Controller\Resque' => 'SafeStartApi\Controller\ResqueController',
            'SafeStartApi\Controller\Jobs' => 'SafeStartApi\Controller\JobsController',
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
                'run-php-resque' => array(
                    'options' => array(
                        'route' => 'resque start [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'SafeStartApi\Controller\Resque',
                            'action' => 'start'
                        )
                    )
                ),
                'run-new-db-checklist-uploaded' => array(
                    'options' => array(
                        'route' => 'resque run new-db-checklist-uploaded --checkListId=',
                        'defaults' => array(
                            'controller' => 'SafeStartApi\Controller\Jobs',
                            'action' => 'processNewDbCheckList'
                        )
                    )
                ),
                'run-new-email-checklist-uploaded' => array(
                    'options' => array(
                        'route' => 'resque run new-email-checklist-uploaded --checkListId= --emails=',
                        'defaults' => array(
                            'controller' => 'SafeStartApi\Controller\Jobs',
                            'action' => 'processNewEmailCheckList'
                        )
                    )
                ),
                'run-checklist-resend' => array(
                    'options' => array(
                        'route' => 'resque run checklist-resend --checkListId= --emails=',
                        'defaults' => array(
                            'controller' => 'SafeStartApi\Controller\Jobs',
                            'action' => 'processCheckListResend'
                        )
                    )
                ),
                'run-ping-email' => array(
                    'options' => array(
                        'route' => 'resque run ping-email',
                        'defaults' => array(
                            'controller' => 'SafeStartApi\Controller\Jobs',
                            'action' => ' pingEmail'
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
            'vehicleReportPdf' => 'SafeStartApi\Controller\Plugin\VehicleReportPdfPlugin',
            'vehicleActionListPdf' => 'SafeStartApi\Controller\Plugin\VehicleActionListPdf',
            'inspectionFaultPdf' => 'SafeStartApi\Controller\Plugin\InspectionFaultPdfPlugin',
            'inspectionPdf' => 'SafeStartApi\Controller\Plugin\InspectionPdfPlugin',
            'GetDataPlugin' => 'SafeStartApi\Controller\Plugin\GetDataPlugin',
            'processTrailerPlugin' => 'SafeStartApi\Controller\Plugin\ProcessTrailerPlugin',
            'queues' => 'SafeStartApi\Controller\Plugin\QueuePlugin',
            'pushNotificationPlugin' => 'SafeStartApi\Controller\Plugin\PushNotificationPlugin',
            'processChecklistPlugin' => 'SafeStartApi\Controller\Plugin\ProcessChecklistPlugin',
        )
    ),
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'SafeStartApi',
                'remember_me_seconds' => 3600,
              //  'cookie_lifetime' => 3600,
               // 'use_cookies' => true,
                'save_path' => __DIR__ . '/../../../data/sessions',
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            'Zend\Session\Validator\RemoteAddr'/*,
            'Zend\Session\Validator\HttpUserAgent',*/
        ),
    ),
);

return array_merge($general, $api);
