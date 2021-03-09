<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Reactphp\Drivers;

use Arikaim\Core\System\Process;
use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Core\Interfaces\QueueWorkerInterface;
use Arikaim\Core\Arikaim;

/**
 *  Queue worker manager driver
 */
class QueueWorkerDriver implements DriverInterface, QueueWorkerInterface
{   
    use Driver;
   
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDriverParams(
            'reactphp-queue',
            'queue.worker',
            'Reactphp Queue Worker',
            'Jobs queue worker create with reactPHP event loop.'
        );
    }

    /**
     * Run worker
     *    
     * @return boolean
     */
    public function run(): bool
    {
        $pid = Process::startBackground($this->getProccessCommand());

        return ($pid > 0);
    }

    /**
     * Get worker process command
     *
     * @return string
     */
    public function getProccessCommand(): string
    {
        $php = (Process::findPhp() === false) ? 'php' : Process::findPhp();        
        return  $php . ' ' . ROOT_PATH . BASE_PATH . '/cli queue:worker >> /dev/null 2>&1 ';
    }

    /**
     * Return true if worker is running
     *    
     * @return boolean
     */
    public function isRunning(): bool
    {
        $pid = Arikaim::get('options')->get('queue.worker.pid',null);
        if (empty($pid) == true) {
            return false;
        }

        $result = Process::isRunning($pid);
        if ($result == false) {
            Arikaim::get('options')->set('queue.worker.pid',null);
        }

        return $result;
    }

    /**
     * Stop worker
     *    
     * @return boolean
     */
    public function stop(): bool
    {
        $pid = Arikaim::get('options')->get('queue.worker.pid',null);
        if (empty($pid) == true) {
            return false;
        }
        
        Process::stop($pid);   

        if ($this->isRunning($pid) == false) {
            Arikaim::get('options')->set('queue.worker.pid','');   
            return true;
        }
      
        return false;
    }

    /**
     * Get title
     *    
     * @return string
     */
    public function getTitle(): string
    {
        return 'ReactPHP Worker';
    }

    /**
     * Get description
     *    
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return 'Jobs queue worker create with reactPHP event loop.';
    }

    /**
     * Get worker service details
     *
     * @return array
     */
    public function getDetails(): array
    {
        return [
            'pid'     => Arikaim::get('options')->get('queue.worker.pid',null),
            'command' => $this->getProccessCommand()
        ];
    }

    /**
     * Init driver
     *
     * @param Properties $properties
     * @return void
     */
    public function initDriver($properties)
    {     
        $config = $properties->getValues();          
    }

    /**
     * Create driver config properties array
     *
     * @param Arikaim\Core\Collection\Properties $properties
     * @return array
     */
    public function createDriverConfig($properties)
    {                     
    }
}
