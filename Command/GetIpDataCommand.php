<?php

namespace YamilovS\SypexGeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetIpDataCommand extends ContainerAwareCommand
{
    protected function configure() {
        $this
            ->setName('yamilovs:sypex-geo:get-ip-data')
            ->setDescription('Get all data about specific ip from database file')
            ->addArgument('ip', InputArgument::REQUIRED, 'IP address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $ip = $input->getArgument('ip');
        $manager = $this->getContainer()->get('yamilovs.sypex_geo.manager');
        $city_data = $manager->getCity($ip);

        $io->title("Data from database file for address $ip");
        $headers = ['Parent', 'Parameter' ,'Value'];
        $rows = [];

        foreach ($city_data as $key => $value) {
            foreach ($value as $k => $v) {
                $rows[] = [$key, $k, $v];
            }
        }

        $io->table($headers, $rows);
    }
}