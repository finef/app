<?php

namespace App\Module;

use Fine\Application\HttpKernel;
use \Fine\Event;
use \Fine\Container\Container;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

require __DIR__ . '/global.php';

class Module
{

    protected $_fine;

    public function register($_fine)
    {
        $this->fine = $fine;

        $fine->event->on('bootstrap', [$this, 'bootstrap']);
    }

    public function bootstrap(Event $event)
    {
        $this->httpkernel($this->request, $this->response)->send();
    }
    
    protected function _mod()
    {
        $fine = $this->_fine;
        $mod = new Container();
        
        $mod([
            'app' => function () use ($mod, $fine) {
                return $mod->app = (new Fine\App\Module\App())->setFine($fine);
            },
        ]);
            
        return $this->mod = $mod;
    }
    
    protected function _httpkernel()
    {
        $this->_fine->mod->each()->app->httpkernel($this->_fine->event);

        return $this->httpkernel = (new HttpKernel())->defineEvent(function() {
            return (new Event())->setFine($this->_fine)->setId('app.kernel')->setDispatcher($this->_fine->event);
        });
    }

    protected function _request()
    {

    }

    protected function _response()
    {

    }
    
    protected function _controller()
    {
        $this->controller = new Container();
        $this->fine->mod->each()->app->controllerGroup($this->controller);
        return $this->controller;
    }
    
}
