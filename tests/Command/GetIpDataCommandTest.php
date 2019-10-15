<?php

declare(strict_types=1);

namespace Yamilovs\Bundle\SypexGeoBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Yamilovs\Bundle\SypexGeoBundle\Command\GetIpDataCommand;
use Yamilovs\SypexGeo\City;
use Yamilovs\SypexGeo\SypexGeo;

class GetIpDataCommandTest extends TestCase
{
    /**
     * @var MockObject|SypexGeo
     */
    private $sypexGeo;

    /**
     * @var CommandTester
     */
    private $commandTester;

    public function setUp(): void
    {
        $this->sypexGeo = $this->createMock(SypexGeo::class);
        $this->commandTester = new CommandTester(new GetIpDataCommand($this->sypexGeo));
    }

    public function testEmptyArguments(): void
    {
        $this->expectException(RuntimeException::class);

        $this->commandTester->execute(['ip']);
    }

    public function testExecute(): void
    {
        $ip = '1.2.3.4';

        $city = (new City())
            ->setId(1)
            ->setLongitude(1.23)
            ->setLatitude(2.34)
            ->setNameRu('Название города')
            ->setNameEn('City name');

        $city->getCountry()
            ->setId(2)
            ->setLongitude(3.45)
            ->setLatitude(4.56)
            ->setIso('US')
            ->setNameRu('Название страны')
            ->setNameEn('Country name');

        $city->getRegion()
            ->setId(3)
            ->setIso('WS')
            ->setNameRu('Название региона')
            ->setNameEn('Region name');

        $this->sypexGeo->expects($this->once())
            ->method('getCity')
            ->with($ip, true)
            ->willReturn($city);

        $this->commandTester->execute(['ip' => $ip]);
        $output = $this->commandTester->getDisplay();
        $strings = explode(PHP_EOL, $output);

        $this->assertStringContainsString((string)$city->getId(), $strings[7]);
        $this->assertStringContainsString((string)$city->getLatitude(), $strings[8]);
        $this->assertStringContainsString((string)$city->getLongitude(), $strings[9]);
        $this->assertStringContainsString($city->getNameRu(), $strings[10]);
        $this->assertStringContainsString($city->getNameEn(), $strings[11]);
        $this->assertStringContainsString((string)$city->getCountry()->getId(), $strings[15]);
        $this->assertStringContainsString($city->getCountry()->getIso(), $strings[16]);
        $this->assertStringContainsString((string)$city->getCountry()->getLatitude(), $strings[17]);
        $this->assertStringContainsString((string)$city->getCountry()->getLongitude(), $strings[18]);
        $this->assertStringContainsString($city->getCountry()->getNameRu(), $strings[19]);
        $this->assertStringContainsString($city->getCountry()->getNameEn(), $strings[20]);
        $this->assertStringContainsString((string)$city->getRegion()->getId(), $strings[24]);
        $this->assertStringContainsString($city->getRegion()->getIso(), $strings[25]);
        $this->assertStringContainsString($city->getRegion()->getNameRu(), $strings[26]);
        $this->assertStringContainsString($city->getRegion()->getNameEn(), $strings[27]);
    }
}