<?php

declare(strict_types=1);

namespace Yamilovs\Bundle\SypexGeoBundle\Command;

use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yamilovs\SypexGeo\SypexGeo;

class GetIpDataCommand extends Command
{
    /**
     * @var SypexGeo
     */
    private $sypexGeo;

    public function __construct(SypexGeo $sypexGeo)
    {
        $this->sypexGeo = $sypexGeo;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('yamilovs:sypex-geo:get-ip-data')
            ->setDescription('Get all data about specific ip from database file')
            ->addArgument('ip', InputArgument::REQUIRED, 'IP address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $ip = $input->getArgument('ip');

        $city = $this->sypexGeo->getCity($ip, true);

        $io->title(sprintf("Data from database file for address '%s'", $ip));
        $rows = [];

        $data = new \ArrayIterator($this->parseData($city));

        while ($data->valid()) {
            $rows[] = [new TableCell($data->key(), ['colspan' => 2])];
            $rows[] = new TableSeparator();

            foreach ($data->current() as $key => $value) {
                $rows[] = [$key, $value];
            }

            $data->next();

            if ($data->valid()) {
                $rows[] = new TableSeparator();
            }
        }

        $io->table([], $rows);
    }

    private function parseData(object $class): array
    {
        $result = [];
        $methods = get_class_methods($class);

        foreach ($methods as $method) {
            if (strpos($method, 'get') !== 0) {
                continue;
            }

            $return = $class->$method();

            if (is_object($return)) {
                $result += $this->parseData($return);
            } else {
                $result[(new ReflectionClass($class))->getShortName()][substr($method, 3)] = $return;
            }
        }

        return $result;
    }
}
