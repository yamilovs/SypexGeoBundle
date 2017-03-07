<?php

namespace YamilovS\SypexGeoBundle\Tests\Manager;

use YamilovS\SypexGeoBundle\Manager\SypexGeoManager;

class SypexGeoManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAvailableModes()
    {
        $availableModes = SypexGeoManager::getAvailableModes();
        $mappings = [
            'SXGEO_FILE'   => SypexGeoManager::SXGEO_FILE,
            'SXGEO_MEMORY' => SypexGeoManager::SXGEO_MEMORY,
            'SXGEO_BATCH'  => SypexGeoManager::SXGEO_BATCH,
        ];

        foreach ($availableModes as $key => $value) {
            $this->assertArrayHasKey($key, $mappings, "Can't found key '$key' in correct mapping list.");
            $this->assertEquals($value, $mappings[$key], "A value '$value' is not correct for key '$key'");
        }
    }
}