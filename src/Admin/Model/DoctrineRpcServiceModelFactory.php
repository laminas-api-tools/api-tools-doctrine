<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin\Model;

use Laminas\ApiTools\Admin\Model\RpcServiceModelFactory;

class DoctrineRpcServiceModelFactory extends RpcServiceModelFactory
{
    /**
     * @param string $module
     * @return DoctrineRpcServiceModel
     */
    public function factory($module)
    {
        if (isset($this->models[$module])) {
            return $this->models[$module];
        }

        $moduleName   = $this->modules->normalizeModuleName($module);
        $moduleEntity = $this->moduleModel->getModule($moduleName);
        $config       = $this->configFactory->factory($module);

        $this->models[$module] = new DoctrineRpcServiceModel($moduleEntity, $this->modules, $config);

        return $this->models[$module];
    }
}
