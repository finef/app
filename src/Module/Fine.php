<?php

namespace Fine\App\Module;

class Fine implements FineAwareInterface
{
    
    use FineAwareTrait;
    
    public function register()
    {
        $this->_fine->event->on('fine.bootstrap', [$this, 'bootstrap']);
    }
    
    public function bootstrap()
    {
        $app = $this->_fine->mod->app;
        $app->httpkernel->handle($app->request, $app->response)->send();
        
    }
    
}
