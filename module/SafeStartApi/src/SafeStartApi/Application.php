<?php

namespace SafeStartApi;


class Application
{

    private static $serviceLocator = null;

    public static function setCurrentControllerServiceLocator(\Zend\ServiceManager\ServiceManager $sl)
    {
        self::$serviceLocator = $sl;
    }

    public static function getCurrentControllerServiceLocator()
    {
        return self::$serviceLocator;
    }

    public static function getSessionManager()
    {
        return self::$serviceLocator ? self::$serviceLocator->get('Zend\Session\SessionManager') : null;
    }

    public static function getAuthService()
    {
        return self::$serviceLocator ? self::$serviceLocator->get('doctrine.authenticationservice.orm_default') : null;
    }

    public static function getCurrentUser()
    {
        return self::getAuthService()->hasIdentity() ? self::getAuthService()->getStorage()->read() : null;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}