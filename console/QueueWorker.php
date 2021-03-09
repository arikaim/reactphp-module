<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Modules\Reactphp\Console;

use React\EventLoop\Factory;

use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\System\Process;
use Arikaim\Core\System\System;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Utils\DateTime;

/**
 * Queue worker 
 */
class QueueWorker extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('queue:worker');
        $this->setDescription('Queue worker');
    }

    /**
     * Run command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input, $output)
    {
        // unlimited execution time
        System::setTimeLimit(0);
        Process::setTitle('arikaim-queue-worker'); 
        // Set time zone
        DateTime::setTimeZone(Arikaim::options()->get('time.zone'));
        $this->showTitle('Queue worker');
        $pid = Process::getCurrentPid();
        Arikaim::get('options')->set('queue.worker.pid',$pid);
      
        $loop = Factory::create();
    
        $loop->addPeriodicTimer(1,function() { 
            $job = Arikaim::get('queue')->getNext();
            if (\is_null($job) == false) {
                $job = Arikaim::get('queue')->executeJob($job);
            }         
        });
      
        $loop->run();  
    }
}
