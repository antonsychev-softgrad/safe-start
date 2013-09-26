<?php

$env = getenv('APP_ENV') ? getenv('APP_ENV') : 'dev';

$modules =  array(
    'DoctrineModule',
    'AssetManager',
    'DoctrineORMModule',
    'SafeStartApp',
    'SafeStartApi',
);

if ($env == 'dev') {
 //   $modules[] = 'ZendDeveloperTools';
}

return array(
    // This should be an array of module namespaces used in the application.
    'modules' => $modules,

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => array(
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => array(
            './module',
            './vendor',
        ),

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively override configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => array(
            sprintf('config/autoload/{,*.}{global,%s,local}.php', $env)
        ),

        // Use the $env value to determine the state of the flag
        'config_cache_enabled' => ($env == 'prod'),

        'config_cache_key' => 'safe_start_api',

        // Use the $env value to determine the state of the flag
        'module_map_cache_enabled' => ($env == 'prod'),

        'module_map_cache_key' => 'safe_start_api_module_map',

        'cache_dir' => 'data/cache/modulecache',

        // Use the $env value to determine the state of the flag
        'check_dependencies' => ($env != 'prod'),
    ),

    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => array(
    //     array(
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ),
    // )

   // Initial configuration with which to seed the ServiceManager.
   // Should be compatible with Zend\ServiceManager\Config.
   // 'service_manager' => array(),
);
