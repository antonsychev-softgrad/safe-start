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

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}