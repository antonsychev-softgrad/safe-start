<?php

namespace SafeStartApi;


class Application
{

    private static $serviceLocator = null;

    private static $em = null;

    private static  $cache = null;

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

    public static function getEntityManager()
    {
        if (!self::$em) self::$em = self::$serviceLocator->get('Doctrine\ORM\EntityManager');
        return self::$em;
    }

    public static function getCache()
    {
        if (self::$cache == NULL) {
            if (version_compare(phpversion('apc'), '3.1.6') >= 0) {
                self::$cache = \Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name' => 'apc',
                        'options' => array(
                            'namespace' => 'presslife',
                        ),
                    ),
                    'plugins' => array(
                        'exception_handler' => array('throw_exceptions' => false),
                    ),
                ));
            } else {
                self::$cache = \Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name' => 'filesystem',
                        'options' => array(
                            'namespace' => 'presslife',
                            'cache_dir' => self::getFileSystemPath('data/cache/'),
                        ),
                    ),
                    'plugins' => array(
                        'exception_handler' => array('throw_exceptions' => false),
                        'serializer'
                    ),
                ));
            }
        }
        return self::$cache;
    }

    public static function getFileSystemPath($fEndPath = null)
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        if (!file_exists($root . "/init_autoloader.php")) {
            $root = dirname($root);
        }

        if ($fEndPath === null || !is_string($fEndPath)) {
            $moduleConfig = self::$serviceLocator->get('Config');
            $fEndPath = isset($moduleConfig['defUsersPath']) ? $moduleConfig['defUsersPath'] : '/';
        }

        $fEndPath = str_replace("{$root}", '', $fEndPath);
        $fEndPath = str_replace('\\', '/', $fEndPath);

        if (preg_match('/^(\/|.\/).*/isU', $fEndPath, $match)) {
            $fEndPath = preg_replace('/^(\/|.\/).*/isU', "", $fEndPath);
        } else {
            $fEndPath = preg_replace('/^(.*)$/isU', "$1", $fEndPath);
        }

        $returnFolder = $root . '/' . $fEndPath;
        if (!preg_match('/.*(\/)$/isU', $returnFolder, $match)) {
            $returnFolder .= '/';
        }

        return $returnFolder;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}