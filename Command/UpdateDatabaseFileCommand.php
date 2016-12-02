<?php

namespace YamilovS\SypexGeoBundle\Command;

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
            $https = [];

            if (isset($proxy['host'])) {
                $https = array_merge_recursive($https, [
                    'method' => 'GET',
                    'request_fulluri' => true,
                    'timeout' => 10,
                    'proxy' => 'tcp://' . $proxy['host'],
                ]);
            }
            if (isset($proxy['auth'])) {
                $https = array_merge_recursive($https, [
                    'header' => [
                        'Proxy-Authorization: Basic ' . $proxy['auth'],
                    ]
                ]);
            }
            $options['https'] = $https;
        }

        return stream_context_create($options);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $this->getContainer()->getParameter('yamilovs_sypex_geo.database_path');
        $fileLocator = $this->getContainer()->get('file_locator');
        $tmpFileName = sha1(uniqid(mt_rand(), true));
        $tmpFilePath = tempnam(sys_get_temp_dir(), $tmpFileName);
        $archive = file_get_contents(self::DATABASE_FILE_LINK, false, $this->getStreamContext($output));
        $zip = new \ZipArchive;

        $output->writeln('<info>Load database from ' . self::DATABASE_FILE_LINK . '</info>');

        try {
            $path = $fileLocator->locate($configPath);
        } catch (\Exception $e) {
            $bundle = substr($configPath, 0, strrpos($configPath, '/'));
            $file = strrchr($configPath, '/');
            $path = $fileLocator->locate($bundle) . $file;
        }

        if ($archive === false) {
            $output->writeln('<error>Cannot download new database file</error>');
        } else {
            file_put_contents($tmpFilePath, $archive);
        }

        if ($zip->open($tmpFilePath) === true) {
            $newDatabaseFile = $zip->getFromName(self::DATABASE_FILE_NAME);
            file_put_contents($path, $newDatabaseFile);
            $zip->close();
            $output->writeln("<info>New database file was saved to: $path</info>");
        } else {
            $output->writeln('<error>Cannot open zip archive</error>');
        }
    }
}
