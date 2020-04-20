<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Recaptcha;

use React\EventLoop\Factory;

use Arikaim\Core\Extension\Module;

/**
 * ReactPhp module class
 */
class ReactPhp extends Module
{   
    /**
     * Event loop instance
     *
     * @var LoopInterface
     */
    private $loop;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loop = $this->createLoop();
    }

    /**
     * Create loop instance
     *
     * @return mixed
     */
    public function createLoop()
    {
        return Factory::create();
    }

    /**
     * Get event loop instance
     *
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Test module
     *
     * @return boolean
     */
    public function test()
    {
        return class_exists(Factory::class);
    }
}
