<?php

namespace Yamilovs\Bundle\SypexGeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDatabaseFileCommand extends ContainerAwareCommand
{
    const DATABASE_FILE_LINK = 'https://sypexgeo.net/files/SxGeoCity_utf8.zip';
    const DATABASE_FILE_NAME = 'SxGeoCity.dat';

    protected function configure()
    {
        $this
            ->setName('yamilovs:sypex-geo:update-database-file')
            ->setDescription('Download and extract new database file to database path');
    }

    protected function getStreamContext(OutputInterface $output)
    {
        $connection = $this->getContainer()->getParameter('yamilovs_sypex_geo.connection');
        $options = [];

        if (empty($connection)) {
            return null;
        }

        if (isset($connection['proxy'])) {
            $output->writeln('<info>Using proxy settings for connection</info>');
            $proxy = $connection['proxy'];
            $http = [];

            if (isset($proxy['host'])) {
                $http = array_merge_recursive($http, [
                    'method' => 'GET',
                    'request_fulluri' => true,
                    'timeout' => 10,
                    'proxy' => 'tcp://' . $proxy['host'],
                ]);
            }
            if (isset($proxy['auth'])) {
                $http = array_merge_recursive($http, [
                    'header' => [
                        'Proxy-Authorization: Basic ' . base64_encode($proxy['auth']),
                    ]
                ]);
            }
            $options['http'] = $http;
        }

        return stream_context_create($options);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $databasePath = $this->getContainer()->getParameter('yamilovs_sypex_geo.database_path');
        $filesystem = $this->getContainer()->get('filesystem');
        $tmpFileName = sha1(uniqid(mt_rand(), true));
        $tmpFilePath = tempnam(sys_get_temp_dir(), $tmpFileName);
        $archive = file_get_contents(self::DATABASE_FILE_LINK, false, $this->getStreamContext($output));
        $zip = new \ZipArchive();

        $output->writeln('<info>Load database from ' . self::DATABASE_FILE_LINK . '</info>');

        if ($archive === false) {
            $output->writeln('<error>Cannot download new database file</error>');
        } else {
            $filesystem->dumpFile($tmpFilePath, $archive);
        }

        if ($zip->open($tmpFilePath) === true) {
            $newDatabaseFile = $zip->getFromName(self::DATABASE_FILE_NAME);
            $filesystem->dumpFile($databasePath, $newDatabaseFile);
            $zip->close();
            $filesystem->remove($tmpFilePath);
            $output->writeln("<info>New database file was saved to: $databasePath</info>");
        } else {
            $output->writeln('<error>Cannot open zip archive</error>');
        }
    }
}
