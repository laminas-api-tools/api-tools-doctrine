<?php

declare(strict_types=1);

namespace Laminas\ApiTools\Doctrine\Admin;

class Module
{
    /**
     * Returns configuration to merge with application configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/admin.config.php';
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies()
    {
        return ['Laminas\ApiTools\Admin'];
    }
}
