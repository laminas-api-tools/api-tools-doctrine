<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\Doctrine;

use Laminas\Mvc\Application;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class TestCase extends AbstractHttpControllerTestCase
{
    private $enabledModules = [];

    public function setApplicationConfig($config)
    {
        $r = (new \ReflectionClass(Application::class))->getConstructor();
        $appVersion = $r->getNumberOfRequiredParameters() === 2 ? 2 : 3;

        if ($appVersion === 3) {
            array_unshift($config['modules'], 'Laminas\Router', 'Laminas\Hydrator');
        }

        $this->enabledModules = $config['module_listener_options']['module_paths'];
        $this->clearAssets();

        parent::setApplicationConfig($config);
    }

    protected function tearDown()
    {
        $this->clearAssets();

        return parent::tearDown();
    }

    private function removeDir($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->removeDir($path) : unlink($path);
        }

        return rmdir($dir);
    }

    private function clearAssets()
    {
        foreach ($this->enabledModules as $module => $path) {
            $configPath = sprintf('%s/config/', $path);
            foreach (glob(sprintf('%s/src/%s/V*', $path, $module)) as $dir) {
                $this->removeDir($dir);
            }
            copy($configPath . '/module.config.php.dist', $configPath . '/module.config.php');
        }
    }

    protected function setModuleName($resource, $moduleName)
    {
        $r = new \ReflectionObject($resource);
        $prop = $r->getProperty('moduleName');
        $prop->setAccessible(true);
        $prop->setValue($resource, $moduleName);
    }
}
