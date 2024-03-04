<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Reactphp;

use React\EventLoop\Factory;

use Arikaim\Core\Extension\Module;

/**
 * ReactPhp module class
 */
class ReactPhp extends Module
{   
    /**
     * Install module
     *
     * @return void
     */
    public function install()
    {
        $this->installDriver('Arikaim\\Modules\\Reactphp\\Drivers\\ReactPhpQueueWorkerDriver');
        $this->registerConsoleCommand('QueueWorkerCommand');
    }

    /**
     * Create loop instance
     *
     * @return mixed
     */
    public function getInstance()
    {
        return Factory::create();
    }

    /**
     * Test module
     *
     * @return boolean
     */
    public function test()
    {
        return \class_exists(Factory::class);
    }
}
