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
use Arikaim\Core\Interfaces\WorkerManagerInterface;
use Arikaim\Core\Arikaim;
use Exception;

/**
 *  Queue worker manager driver
 */
class QueueWorkerDriver implements DriverInterface, WorkerManagerInterface
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
        $result = $this->isRunning();
        if ($result == true) {
            return true;
        }

        Process::startBackground($this->getProccessCommand());
        sleep(2);

        return $this->isRunning();
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost(): string 
    {
        return '0.0.0.0';
    }

    /**
     * Get port
     *
     * @return string
     */
    public function getPort(): string 
    {
        return '3080';
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
     * Get worker process command
     *
     * @return string
     */
    public function getProccessCommand(): string
    {
        $php = (Process::findPhp() === false) ? 'php' : Process::findPhp();   

        return $php . ' ' . ROOT_PATH . BASE_PATH . '/cli queue:worker ';
    }

    /**
     * Return true if worker is running
     *    
     * @return boolean
     */
    public function isRunning(): bool
    {
        try {          
            $response = Arikaim::get('http')->put($this->getUrl(),[
                'form_params' => [
                    'command' => 'alive'
                ]
            ]);
    
            $json = $response->getBody()->getContents();
            $result = \json_decode($json,true);
            if (\is_array($result) == false) {
                return false;
            }

            return ($result['result']['status'] == 1);
            
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Stop worker
     *    
     * @return boolean
     */
    public function stop(): bool
    {
        try {
            Arikaim::get('http')->put($this->getUrl(),[
                'form_params' => [
                    'command' => 'stop'
                ]
            ]);          
        } catch (Exception $e) {          
        }

        return ($this->isRunning() == false);
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
            'command' => $this->getProccessCommand(),
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
    }
}
