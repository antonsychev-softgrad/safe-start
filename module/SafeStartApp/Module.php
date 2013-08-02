<?php

namespace SafeStartApp;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

class Module
{
   /* public function init(ModuleManager $moduleManager)
    {
        $config = $this->getServiceLocator()->get('Config');
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        // set empty layout on dispatch event
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', function ($e) use ($config) {
            $controller = $e->getTarget();
            $controller->config = $config;
        }, 100);
    }*/

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
