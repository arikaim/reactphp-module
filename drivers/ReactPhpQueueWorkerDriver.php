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

use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Core\Interfaces\WorkerManagerInterface;

/**
 *  Queue worker manager driver
 */
class ReactPhpQueueWorkerDriver implements DriverInterface, WorkerManagerInterface
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
        return false;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost(): string 
    {
        return '127.0.0.1';
    }

    /**
     * Get port
     *
     * @return string
     */
    public function getPort(): string 
    {
        return '3000';
    }

    /**
     * Get url 
     *
     * @return string
     */
    protected function getUrl(): string
    {
        return $this->getHost() . ':' . $this->getPort();
    }

    /**
     * Return true if worker is running
     *    
     * @return boolean
     */
    public function isRunning(): bool
    {
        return false;
    }

    /**
     * Stop worker
     *    
     * @return boolean
     */
    public function stop(): bool
    {
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
            'host'    => $this->getHost(),
            'port'    => $this->getPort()
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
     * @return void
     */
    public function createDriverConfig($properties)
    {         
        $properties->property('interval',function($property) {
            $property
                ->title('Loop Interval')
                ->type('list')
                ->default('1')
                ->value('1')
                ->items([1,5])
                ->readonly(false);              
        });             
    }
}
