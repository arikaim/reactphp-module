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

use Arikaim\Modules\Reactphp\Worker\ReactPhpQueueWorker;
use Arikaim\Core\Console\ConsoleCommand;
use Arikaim\Core\System\System;
use Arikaim\Core\Arikaim;

/**
 * Queue worker 
 */
class QueueWorkerCommand extends ConsoleCommand
{  
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('queue:worker:reactphp');
        $this->setDescription('ReactPhp Queue worker');        
    }

    /**
     * Run command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function executeCommand($input,$output)
    {
        // unlimited execution time
        System::setTimeLimit(0);
        $driver = Arikaim::get('driver')->create('reactphp-queue');
        if ($driver == null) {
            $this->showError('React php queue dievr not installed.');
            return;
        }

        $this->showTitle();

        $worker = new ReactPhpQueueWorker($driver->getHost(),$driver->getPort(),[]);
        $worker->init();
        $worker->onJobExecuted(function($job) {          
            $this->writeFieldLn('Executed job ',$job->getName());
        });

        $worker->run();      
    }
}
