<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Modules\Reactphp\Worker;

use React\EventLoop\Loop;

use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Interfaces\WorkerInterface;
use Closure;

/**
 * Queue worker 
 */
class ReactPhpQueueWorker implements WorkerInterface
{  
    /**
     * Server host
     *
     * @var string
     */
    protected $host;

    /**
     * Server port
     *
     * @var string
     */
    protected $port;

    /**
     * Server options
     *
     * @var array
     */
    protected $options;

    /**
     * Undocumented variable
     *
     * @var null|Closure
     */
    protected $onJobExecutedCallback;

    /**
     * Constructor
     *
     * @param string $host
     * @param string $port
     * @param array $options
     */
    public function __construct(string $host, string $port, array $options = [])
    {
        $this->host = $host;
        $this->port = $port;
        $this->options = $options;
        $this->onJobExecutedCallback = null;
    }

    /**
     * Get option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Init
     *
     * @return void
     */
    public function init(): void 
    {
        global $arikaim;

        DateTime::setTimeZone($arikaim->get('options')->get('time.zone'));     
    }

    /**
     * Stop worker
     *    
     * @return void
     */
    public function stop(): void
    {
    }

    /**
     * Set on job executed callback
     *
     * @param Closure $callback
     * @return void
     */
    public function onJobExecuted(Closure $callback): void
    {
        $this->onJobExecutedCallback = $callback;
    }

    /**
     * Run server
     *
     * @return void
     */
    public function run(): void 
    {
        global $arikaim;

        $interval = $this->getOption('interval',1.0);
        $loop = Loop::get();
        
        $loop->addPeriodicTimer($interval,function() use($arikaim) {   
          
            $job = $arikaim->get('queue')->getNext();
            if ($job !== null) {              
                $job = $arikaim->get('queue')->executeJob($job);

                if (\is_callable($this->onJobExecutedCallback) == true && $job != null) {                  
                    ($this->onJobExecutedCallback)($job);
                }
            }        
             
        });

        $loop->run();
    }   
}
