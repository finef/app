<?php

namespace App\Module;

use Fine\Application\HttpKernel;
use Fine\Event;
use Fine\Container\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

require __DIR__ . '/global.php';

class Module implements FineAwareInterface
{

    use FineAwareTrait;
    use FineDistributeTrait;
    
    protected function _hook()
    {
        return $this->hook = (new FineContainer())->__invoke([
            'app' => Fine\App\Module\App::class,
            'fine' => Fine\App\Module\Fine::class,
        ]);
    }
    
    protected function _httpkernel()
    {
        $fine = $this->_fine;
        
        $fine->mod->hook()->app->httpkernel($fine->event);

        return $this->httpkernel = (new HttpKernel())->setEventFactory(function() use ($fine) {
            return (new Event())->setFine($fine)->setId('app.httpkernel')->setDispatcher($fine->event);
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
        return $this->_fine->mod->hook()->app->controllerGroup(new Container());
    }
    
    protected function _view()
    {
        return $this->_fine->mod->hook()->app->view(new FineContainer());
    }
    
    protected function _render()
    {
        
        $fine = $this->_fine;
        
        $fine->mod->hook()->app->render($fine->event);
        
        return $this->render = (new Renderer())->setEventFactory(function() use ($fine) {
            return (new Event())->setFine($fine)->setId('app.render')->setDispatcher($fine->event);
        });
    }


    protected function _resource()
    {
//        return $this->
    }
    
}
