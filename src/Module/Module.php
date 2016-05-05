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

    protected $fine;

    public function register($fine)
    {
        $this->fine = $fine;

        $fine([
            'db' => function() use ($fine) {
                return $fine->db = $fine->mod->app->db;
            },
            'router' => function() use ($fine) {
                return $fine->router = $fine->mod->app->router;
            },
        ]);

        $fine->event->on('bootstrap', [$this, 'bootstrap']);
    }

    public function bootstrap(Event $event)
    {
        $this->httpkernel($this->request, $this->response)->send();
    }
    
    protected function _mod()
    {
        return $this->mod = (new Container())->__invoke([
            'app' => '\Fine\App\Module\App',
        ]);
    }
    
    protected function _httpkernel()
    {
        $this->fine->mod->each()->app->httpkernel($this->fine->event);

        return $this->httpkernel = (new HttpKernel())->defineEvent(function() {
            return (new Event())->setFine($this->fine)->setId('app.kernel')->setDispatcher($this->fine->event);
        });
 }

    protected function _request()
    {

    }

    protected function _response()
    {

    }
}
