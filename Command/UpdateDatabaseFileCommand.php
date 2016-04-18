<?php

namespace YamilovS\SypexGeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class UpdateDatabaseFileCommand extends ContainerAwareCommand
{
    const DATABASE_FILE_LINK = 'https://sypexgeo.net/files/SxGeoCity_utf8.zip';
    const DATABASE_FILE_NAME = 'SxGeoCity.dat';

    protected function configure() {
        $this
            ->setName('yamilovs:sypex-geo:update-database-file')
            ->setDescription('Download and extract new database file to database path')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $tmpFileName = sha1(uniqid(mt_rand(), true));
        $tmpFilePath = tempnam(sys_get_temp_dir(), $tmpFileName);
        $newDatabaseFileArchive = file_get_contents(self::DATABASE_FILE_LINK);
        $path = $this->getContainer()->get('kernel')->locateResource($this->getContainer()->getParameter('yamilovs_sypex_geo.database_path'));
        $zip = new \ZipArchive;

        if ($newDatabaseFileArchive === false) {
            $output->writeln('<error>Cannot download new database file</error>');
        } else {
            file_put_contents($tmpFilePath, $newDatabaseFileArchive);
        }

        if ($zip->open($tmpFilePath) === true) {
            $newDatabaseFile = $zip->getFromName(self::DATABASE_FILE_NAME);
            file_put_contents($path, $newDatabaseFile);
            $zip->close();
        } else {
            $output->writeln('<error>Cannot open zip archive</error>');
        }

        $output->writeln("<info>New database file was saved to: $path</info>");
    }
}