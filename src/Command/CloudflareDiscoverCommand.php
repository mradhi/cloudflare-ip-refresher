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

use App\Event\InternetCheckEvent;
use App\Event\IPDiscoverEvent;
use App\IP\InternetChecker;
use App\IP\IPDiscover;
use Exception;
use Sentry\Severity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Sentry\captureException;
use function Sentry\captureMessage;

class CloudflareDiscoverCommand extends Command
{
    protected static $defaultName = 'ip:cloudflare:discover';

    /**
     * @var InternetChecker
     */
    protected $internetChecker;

    /**
     * @var IPDiscover
     */
    protected $ipDiscover;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(InternetChecker $internetChecker, IPDiscover $ipDiscover, EventDispatcherInterface $eventDispatcher)
    {
        $this->internetChecker = $internetChecker;
        $this->ipDiscover      = $ipDiscover;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Discover IP changes and update DNS records.')
            ->setHelp('This command allows you to check if the IP address changed and synchronize it with Cloudflare DNS records');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // We'll use a lock file to avoid running the command multiple times in parallel (asynchronous)
        $lock = (new LockFactory(new FlockStore()))->createLock('ip-cloudflare-discover');

        if ($lock->acquire()) {
            $io = new SymfonyStyle($input, $output);

            // Check internet connection
            $this->eventDispatcher->dispatch(new InternetCheckEvent(
                $isConnected = $this->internetChecker->isConnected()
            ));

            if (!$isConnected) {
                $io->note($message = 'Check the internet connection');

                captureMessage($message, Severity::info());

                $lock->release();

                return 0;
            }

            // Dispatch new IP discovery event / synchronize DNS records
            try {
                $this->eventDispatcher->dispatch(new IPDiscoverEvent(
                    $this->ipDiscover->getIpAddress()
                ));
            } catch (Exception $exception) {
                captureException($exception);

                $io->error($exception->getMessage());

                $lock->release();

                return 1;
            }

            $io->success('Synchronized successfully');

            $lock->release();
        }

        return 0;
    }
}
