<?php
/**
 * This file is part of the Elguen, IP Refresher package.
 *
 * (c) Mohamed Radhi GUENNICHI <guennichiradhi@gmail.com> <+216 50 711 816>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Command;

use App\Service\CloudflareService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

class CloudflareStatusCommand extends Command
{
    protected static $defaultName = 'ip:cloudflare:status';

    /**
     * @var CloudflareService
     */
    protected $cloudflareService;

    public function __construct(CloudflareService $cloudflareService)
    {
        $this->cloudflareService = $cloudflareService;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Check the cloudflare account status.')
            ->setHelp('This command allows you to check the cloudflare status by requesting cloudflare.com api endpoint');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // We'll use a lock file to avoid running the command multiple times in parallel (asynchronous)
        $lock = (new LockFactory(new FlockStore()))->createLock('ip-cloudflare-status');

        if ($lock->acquire()) {
            $io = new SymfonyStyle($input, $output);

            $r = $this->cloudflareService->getDNS()
                ->listRecords($this->cloudflareService->getZoneID('elguen.com'), 'A');

            dump($r);
            $lock->release();
            return 0;

            if (!$this->cloudflareService->isConnected()) {
                $io->error('Could not connect to Cloudflare, check your internet connection/access keys.');

                $lock->release();

                return 1;
            }

            $io->success(
                sprintf('Connected to Cloudflare (%s)', $this->cloudflareService->getUserDetails()->email)
            );

            $lock->release();
        }

        return 0;
    }
}
