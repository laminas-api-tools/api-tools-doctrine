<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Doctrine;

use Laminas\Mvc\Application;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

use function array_diff;
use function array_unshift;
use function copy;
use function glob;
use function is_dir;
use function rmdir;
use function scandir;
use function sprintf;
use function unlink;

use const DIRECTORY_SEPARATOR;

class TestCase extends AbstractHttpControllerTestCase
{
    /** @var string[] */
    private $enabledModules = [];

    /**
     * @param array<string, mixed> $config
     * @return $this
     */
    public function setApplicationConfig($config)
    {
        $r          = (new ReflectionClass(Application::class))->getConstructor();
        $appVersion = $r->getNumberOfRequiredParameters() === 2 ? 2 : 3;

        if ($appVersion === 3) {
            array_unshift($config['modules'], 'Laminas\Router', 'Laminas\Hydrator');
        }

        $this->enabledModules = $config['module_listener_options']['module_paths'];
        $this->clearAssets();

        return parent::setApplicationConfig($config);
    }

    protected function tearDown(): void
    {
        $this->clearAssets();

        parent::tearDown();
    }

    private function removeDir(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->removeDir($path) : unlink($path);
        }

        rmdir($dir);
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

    /** @throws ReflectionException */
    protected function setModuleName(object $resource, string $moduleName): void
    {
        $r    = new ReflectionObject($resource);
        $prop = $r->getProperty('moduleName');
        $prop->setAccessible(true);
        $prop->setValue($resource, $moduleName);
    }
}
