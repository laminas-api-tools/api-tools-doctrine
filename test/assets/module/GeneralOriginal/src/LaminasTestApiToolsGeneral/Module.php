<?php
namespace LaminasTestApiToolsGeneral;

use Laminas\ApiTools\Provider\ApiToolsProviderInterface;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;

class Module implements ApiToolsProviderInterface, BootstrapListenerInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * Add the event catcher
     *
     * @param EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $eventManager = $application->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        $eventCatcher = $serviceManager->get('LaminasTestApiToolsGeneral\Listener\EventCatcher');
        $sharedEventManager->attachAggregate($eventCatcher);
    }

}
