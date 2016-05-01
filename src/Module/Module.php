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

    protected $app;
    protected $httpkernel;

    public function register($app)
    {
        $this->app = $app;

        $app(array(
            'db' =>  function() use ($app) {
                return $app->db = $app->mod->app->db;
            },
            'router' => function() use ($app) {
                return $app->router = $app->mod->app->router;
            },
        ));

        $app->event->on('bootstrap', array($this, 'bootstrap'));
    }

    public function bootstrap(Event $event)
    {
        $this->httpkernel($this->request)->send();
    }

    public function httpkernel(RequestInterface $request, ResponseInterface $response = null)
    {
        if ($this->httpkernel === null) {
            $this->httpkernel = true;
            $this->app->mod->hook()->app->httpkernel($this->app);
        }

        if ($response === null) {
            $response = $this->response;
        }

        return $this->app->event
            ->run(
                (new Event())
                    ->setId('app.httpkernel')
                    ->setRequest($request)
                    ->setResponse($response)
            )
            ->getResponse();
    }

    protected function _hook()
    {
        return $this->hook = new Container(array('__invoke' => array(
            'app'    => '\Fine\App\Module\App',
        )));
    }
    protected function _request()
    {

    }

    protected function _response()
    {

    }
}
