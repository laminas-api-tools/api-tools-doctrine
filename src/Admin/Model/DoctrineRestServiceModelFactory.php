<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Laminas\ApiTools\Admin\Exception;
use Laminas\ApiTools\Admin\Model\RestServiceModelFactory;
use Laminas\EventManager\EventManager;
use Laminas\ServiceManager\ServiceManager;
use ReflectionClass;

use function sprintf;

class DoctrineRestServiceModelFactory extends RestServiceModelFactory
{
    public const TYPE_DEFAULT = DoctrineRestServiceModel::class;

    /** @var ServiceManager */
    protected $serviceManager;

    /**
     * Get service manager
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager
     *
     * @return DoctrineRestServiceModelFactory
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * @param string $module
     * @param string $type
     * @return DoctrineRestServiceModel
     */
    public function factory($module, $type = self::TYPE_DEFAULT)
    {
        if (isset($this->models[$type][$module])) {
            return $this->models[$type][$module];
        }

        $moduleName   = $this->modules->normalizeModuleName($module);
        $config       = $this->configFactory->factory($module);
        $moduleEntity = $this->moduleModel->getModule($moduleName);

        $restModel = new DoctrineRestServiceModel($moduleEntity, $this->modules, $config);
        $restModel->setEventManager($this->createEventManager());
        $restModel->setServiceManager($this->getServiceManager());

        switch ($type) {
            case self::TYPE_DEFAULT:
                $this->models[$type][$module] = $restModel;
                return $restModel;
            default:
                throw new Exception\InvalidArgumentException(sprintf(
                    'Model of type "%s" does not exist or cannot be handled by this factory',
                    $type
                ));
        }
    }

    /**
     * Create and return an EventManager composing the shared event manager instance.
     *
     * @return EventManager
     */
    private function createEventManager()
    {
        $r = new ReflectionClass(EventManager::class);

        if ($r->hasMethod('setSharedManager')) {
            // laminas-eventmanager v2 initialization
            $eventManager = new EventManager();
            $eventManager->setSharedManager($this->sharedEventManager);
            return $eventManager;
        }

        // laminas-eventmanager v3 initialization
        return new EventManager($this->sharedEventManager);
    }
}
