<?php
namespace SafeStartApiTest;

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Console\Console;
use Zend\Console\Exception\RuntimeException as ConsoleException;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class Bootstrap
{
    protected static $serviceManager;
    protected static $config;
    protected static $bootstrap;
    protected static $console;
    protected static $jsonSchemaRetriever;
    protected static $jsonSchemaRefResolver;
    public static $jsonSchemaValidator;

    public static function init()
    {

        $testConfig = include __DIR__ . '/TestConfig.php';

        $zf2ModulePaths = array();

        if (isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePaths = $testConfig['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath) {
                if (($path = static::findParentPath($modulePath))) {
                    $zf2ModulePaths[] = $path;
                }
            }
        }

        $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ? : (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies
        $baseConfig = array(
            'module_listener_options' => array(
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ),
        );

        $config = ArrayUtils::merge($baseConfig, $testConfig);

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();

        static::$serviceManager = $serviceManager;
        static::$config = $config;

        static::$console = Console::getInstance();

        static::$jsonSchemaRetriever = new \JsonSchema\Uri\UriRetriever();
        static::$jsonSchemaRefResolver = new \JsonSchema\RefResolver;
        static::$jsonSchemaValidator = new \JsonSchema\Validator();
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    public static function getConfig()
    {
        return static::$config;
    }

    public static function getJsonSchemaRequest($method = "index/ping") {
        $schemaFile =  __DIR__ . '/../public/schemas/' . $method . '/request.json';
        if (!file_exists($method)) {
            static::$console->write("WARNING: JSON schema request file for ". $method ." not found! \r\n", 2);
            return new \stdClass();
        }
        $schema = static::$jsonSchemaRetriever->retrieve('file://' . $schemaFile);
        return $schema;
    }

    public static function getJsonSchemaResponse($method = "index/ping") {
        $schemaFile =  realpath(__DIR__ . '/../public/schemas/' . $method . '/response.json');
        if (!file_exists($method)) {
            static::$console->write("WARNING: JSON schema response file for ". $method ." not found! \r\n", 2);
         //   return new \stdClass();
        }
        return static::$jsonSchemaRetriever->retrieve('file://' . $schemaFile);
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        } else {
            $zf2Path = getenv('ZF2_PATH') ? : (defined('ZF2_PATH') ? ZF2_PATH : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

            if (!$zf2Path) {
                throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
            }

            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';

        }

        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                ),
            ),
        ));
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}

Bootstrap::init();