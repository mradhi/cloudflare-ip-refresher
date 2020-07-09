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
use App\IP\InternetChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InternetStatusCommand extends Command
{
    protected static $defaultName = 'ip:internet:status';

    /**
     * @var InternetChecker
     */
    protected $internetChecker;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(InternetChecker $internetChecker, EventDispatcherInterface $eventDispatcher)
    {
        $this->internetChecker = $internetChecker;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Check the internet status.')
            ->setHelp('This command allows you to check the internet status by opening a socket to google.com');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // We'll use a lock file to avoid running the command multiple times in parallel (asynchronous)
        $lock = (new LockFactory(new FlockStore()))->createLock('ip-internet-status');

        if ($lock->acquire()) {
            $io = new SymfonyStyle($input, $output);

            $this->eventDispatcher->dispatch(
                new InternetCheckEvent(
                    $isConnected = $this->internetChecker->isConnected()
                )
            );

            if (!$isConnected) {
                $io->error('The machine is not isConnected to the internet, try again.');

                return 1;
            }

            $io->success('The machine is isConnected to the internet.');

            $lock->release();
        }

        return 0;
    }
}
