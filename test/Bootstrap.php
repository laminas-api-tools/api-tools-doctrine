<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-doctrine for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-doctrine/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools;

use Laminas\Loader\AutoloaderFactory;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);
date_default_timezone_set('UTC');

/**
 * Test bootstrap, for setting up autoloading
 *
 * @subpackage UnitTest
 */
class Bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        static::initAutoloader();

        // Create testing modules
        $run = "rm -rf " . __DIR__ . "/assets/module/Db";
        exec($run);

        $run = "rm -rf " . __DIR__ . "/assets/module/DbApi";
        exec($run);

        mkdir(__DIR__ . '/assets/module/Db');
        mkdir(__DIR__ . '/assets/module/DbApi');

        $run = 'rsync -a ' . __DIR__ . '/assets/module/DbOriginal/* ' . __DIR__ . '/assets/module/Db';
        exec($run);

        $run = 'rsync -a ' . __DIR__ . '/assets/module/DbApiOriginal/* ' . __DIR__ . '/assets/module/DbApi';
        exec($run);

        // Create testing modules
        $run = "rm -rf " . __DIR__ . "/assets/module/DbMongo";
        exec($run);

        $run = "rm -rf " . __DIR__ . "/assets/module/DbMongoApi";
        exec($run);

        mkdir(__DIR__ . '/assets/module/DbMongo');
        mkdir(__DIR__ . '/assets/module/DbMongoApi');

        $run = 'rsync -a ' . __DIR__ . '/assets/module/DbMongoOriginal/* ' . __DIR__ . '/assets/module/DbMongo';
        exec($run);

        $run = 'rsync -a ' . __DIR__ . '/assets/module/DbMongoApiOriginal/* ' . __DIR__ . '/assets/module/DbMongoApi';
        exec($run);

        // Create General module
        $run = "rm -rf " . __DIR__ . "/assets/module/General";
        exec($run);

        mkdir(__DIR__ . '/assets/module/General');

        $run = 'rsync -a ' . __DIR__ . '/assets/module/GeneralOriginal/* ' . __DIR__ . '/assets/module/General';
        exec($run);
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';

            return;
        }

        $laminasPath = getenv('LAMINAS_PATH') ?: (defined('LAMINAS_PATH') ? LAMINAS_PATH : (is_dir($vendorPath . '/Laminas/library') ? $vendorPath . '/Laminas/library' : false));

        if (!$laminasPath) {
            throw new RuntimeException('Unable to load Laminas. Run `php composer.phar install` or define a LAMINAS_PATH environment variable.');
        }

        if (isset($loader)) {
            $loader->add('Laminas', $laminasPath . '/Laminas');
        } else {
            include $laminasPath . '/Laminas/Loader/AutoloaderFactory.php';
            AutoloaderFactory::factory(
                array(
                'Laminas\Loader\StandardAutoloader' => array(
                    'autoregister_laminas' => true,
                    'namespaces' => array(
                        'Laminas\ApiTools\Doctrine' => __DIR__ . '/../src',
                        __NAMESPACE__ => __DIR__,
                        'Test' => __DIR__ . '/../vendor/Test/',
                    ),
                ),
                )
            );
        }
    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }
}

Bootstrap::init();
