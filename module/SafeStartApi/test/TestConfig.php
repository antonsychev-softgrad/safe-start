<?php

return array(
    'modules' => array(
        'DoctrineModule',
        'AssetManager',
        'DoctrineORMModule',
        'SafeStartApi',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/autoload/{,*.}{global,test,local}.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
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
);