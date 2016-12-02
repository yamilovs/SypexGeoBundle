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
    
    protected function createContext(OutputInterface $out)
    {
        $proxy = $this->getContainer()->getParameter('yamilovs_sypex_geo.proxy');
        if (!empty($proxy['host'])){
            $arr = [
                'method' => 'GET',
                'timeout' => 10,
                'proxy' => 'tcp://'.$proxy['host'],
                'request_fulluri' => true,
            ];
            $out->writeln("<info>Use proxy: {$proxy['host']}</info>");
            if (!empty($proxy['auth'])){
                $out->writeln("<info>Use proxy auth</info>");
                $auth = $auth = base64_encode($proxy['auth']);
                $arr['header'] = "Proxy-Authorization: Basic $auth";
            }
            $context = stream_context_create(
                [
                    'http' => $arr
                ]
            );
        }else{
            $context = stream_context_create();
        }
        return $context;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $configPath     = $this->getContainer()->getParameter('yamilovs_sypex_geo.database_path');
        $fileLocator    = $this->getContainer()->get('file_locator');
        $tmpFileName    = sha1(uniqid(mt_rand(), true));
        $tmpFilePath    = tempnam(sys_get_temp_dir(), $tmpFileName);
        $streamContext  = $this->createContext($output);
        $output->writeln("<info>Load database from ".self::DATABASE_FILE_LINK."</info>");
        $archive        = file_get_contents(self::DATABASE_FILE_LINK, null, $streamContext);
        $zip            = new \ZipArchive;

        try {
            $path = $fileLocator->locate($configPath);
        } catch (\Exception $e) {
            $bundle = substr($configPath, 0, strrpos($configPath, '/'));
            $file   = strrchr($configPath, '/');
            $path   = $fileLocator->locate($bundle).$file;
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
        } else {
            $output->writeln('<error>Cannot open zip archive</error>');
        }

        $output->writeln("<info>New database file was saved to: $path</info>");
    }
}
