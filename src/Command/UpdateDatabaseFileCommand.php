<?php

declare(strict_types=1);

namespace Yamilovs\Bundle\SypexGeoBundle\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class UpdateDatabaseFileCommand extends Command
{
    private const DATABASE_FILE_LINK = 'https://sypexgeo.net/files/SxGeoCity_utf8.zip';
    private const DATABASE_FILE_NAME = 'SxGeoCity.dat';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $databasePath;

    /**
     * @var array
     */
    private $connection;

    public function __construct(Filesystem $filesystem, string $databasePath, array $connection)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->databasePath = $databasePath;
        $this->connection = $connection;
    }

    protected function configure(): void
    {
        $this
            ->setName('yamilovs:sypex-geo:update-database-file')
            ->setDescription('Download and extract new database file to database path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $zip = new \ZipArchive();
        $client = new Client();

        $io->note(sprintf('Loading database archive file from "%s"', static::DATABASE_FILE_LINK));

        $tmpFilePath = tempnam(sys_get_temp_dir(), sha1(uniqid((string)mt_rand(), true)));

        $settings = [
            'sink' => $tmpFilePath,
        ];

        $settings = $this->addProxySettings($settings, $io);
        $settings = $this->addAuthSettings($settings, $io);

        try {
            $client->request(Request::METHOD_GET, static::DATABASE_FILE_LINK, $settings);
        } catch (GuzzleException $e) {
            $io->error(sprintf('%d: %s', $e->getCode(), $e->getMessage()));
            return;
        }

        if (true !== $zip->open($tmpFilePath)) {
            $io->error('Can\'t open database archive');
            return;
        }

        $databaseFile = $zip->getFromName(static::DATABASE_FILE_NAME);
        $this->filesystem->dumpFile($this->databasePath, $databaseFile);
        $this->filesystem->remove($tmpFilePath);

        $zip->close();

        $io->success(sprintf('New database file was saved to "%s"', realpath($this->databasePath)));
    }

    private function addProxySettings(array $settings, SymfonyStyle $io): array
    {
        $host = $this->connection['proxy']['host'] ?? null;
        $port = $this->connection['proxy']['port'] ?? null;

        if ($host && $port) {
            $settings = array_merge($settings, ['proxy' => sprintf('http://%s:%d', $host, $port)]);

            $io->note(sprintf('Using proxy "%s:%d" for connection', $host, $port));
        }

        return $settings;
    }

    private function addAuthSettings(array $settings, SymfonyStyle $io): array
    {
        $user = $this->connection['proxy']['auth']['user'] ?? null;
        $password = $this->connection['proxy']['auth']['password'] ?? null;

        if ($user && $password) {
            $settings = array_merge($settings, ['auth' => [$user, $password]]);

            $io->note(sprintf('With authorization "%s:%s"', $user, $password));
        }

        return $settings;
    }
}
