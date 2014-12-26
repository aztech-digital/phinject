<?php

namespace Aztech\Phinject\Tests\Config;

use Aztech\Phinject\Config\ConfigFactory;
use Aztech\Phinject\Config\Json;
use Aztech\Phinject\Config\PHP;
use Aztech\Phinject\Config\YML;

/**
 * @todo use VFS for files
 * @author thibaud
 *
 */

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function getInvalidPaths()
    {
        /* @f:off */
        return array(
            array('some-file.phpc'),
            array('some-file.c'),
            array('some-file.doc'),
            array('some-file.xls'),
            array('some-file.png'),
            array('some-file.jpg')
        );
        /* @f:on */
    }

    /**
     * @dataProvider getInvalidPaths
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWhenTypeIsUnknown($path)
    {
        $config = ConfigFactory::fromFile('tests/res/config-factory-test-files/' . $path);
    }

    /**
     * @dataProvider getInvalidPaths
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionWhenFileDoesNotExist($path)
    {
        $config = ConfigFactory::fromFile($path);
    }

    public function getValidPaths()
    {
        /* @f:off */
        return array(
            array('config.json', '\Aztech\Phinject\Config\AbstractConfig'),
            array('config.yaml', '\Aztech\Phinject\Config\AbstractConfig'),
            array('config.yml', '\Aztech\Phinject\Config\AbstractConfig'),
            array('config.php', '\Aztech\Phinject\Config\AbstractConfig')
        );
        /* @f:on */
    }

    /**
     * @dataProvider getValidPaths
     */
    public function testReturnsConfigWhenFileTypeIsKnown($path, $class)
    {
        $this->assertInstanceOf($class, ConfigFactory::fromFile('tests/res/config-factory-test-files/' . $path));
    }
}

