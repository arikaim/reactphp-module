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

use React\EventLoop\Factory;
use React\Http\Server;

use Arikaim\Core\Arikaim;
use Arikaim\Core\Utils\DateTime;
use Arikaim\Core\Http\ApiResponse;
use Arikaim\Core\Interfaces\WorkerInterface;
use Exception;

/**
 * Queue worker 
 */
class QueueWorker implements WorkerInterface
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
        DateTime::setTimeZone(Arikaim::options()->get('time.zone'));     
    }

    /**
     * Stop worker
     *    
     * @return void
     */
    public function stop(): void
    {
        echo 'Exit ' . PHP_EOL;
        exit();
    }

    /**
     * Run server
     *
     * @return void
     */
    public function run(): void 
    {
        $loop = Factory::create();
        
        $loop->addPeriodicTimer($this->getOption('interval',1.0),function() {            
            $job = Arikaim::get('queue')->getNext();
            if (\is_null($job) == false) {
                echo 'execute job ' . PHP_EOL;
                $job = Arikaim::get('queue')->executeJob($job);
            }         
        });

        $server = new Server($loop,
            new \React\Http\Middleware\RequestBodyParserMiddleware(),    
            function ($request) {
                $method = $request->getMethod();
                $body = $request->getParsedBody();
                $command = $body['command'] ?? null;
                echo 'Request: ' . $method . '  ' . $request->getUri() . PHP_EOL;
              
                $response = new ApiResponse(Arikaim::response());
                $result = [
                    'host'    => $this->host,
                    'port'    => $this->port,
                    'command' => $command,
                    'status'  => 1
                ];
              
                $response->setResult($result);
                                
                if ($command == 'stop') {
                    $this->stop();
                }

                return $response->getResponse();
        });
        
        $server->on('error',function (Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
        });
                
        $socket = new \React\Socket\Server($this->host . ':' . $this->port,$loop);
        $server->listen($socket);

        $loop->run();
    }   
}
