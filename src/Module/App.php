<?php

namespace Fine\App\Module;

use Fine\Container\Container;
use Fine\Event\Event;
use Fine\Event\EventDispatcher;

class App
{
    
    protected $_fine;

    public function setFine($fine)
    {
        $this->_fine = $fine;
        return $this;
    }

    public function controllerGroup(Container $groups)
    {
        $fine = $this->_fine;
        $groups['app'] = function () use ($fine, $groups) {
            return $groups->app = $fine->mod->each()->app->controller(new Container());
        };
    }
    
    public function controller(Container $controllers)
    {
        $controllers([
           'index' => 'Fine\App\Front\Controller\IndexController',
        ]);
    }
    
    public function httpkernel(EventDispatcher $dispatcher)
    {
        $dispatcher
            ->on('app.httpkernel', [$this, 'onHttpkernelRouter'], 100)
            ->on('app.httpkernel', [$this, 'onHttpkernelGetController'], 200)
            ->on('app.httpkernel', [$this, 'onHttpkernelInjectServices'], 300)
            ->on('app.httpkernel', [$this, 'onHttpkernelDispatchController'], 400)
        ;
        
    }
    
    public function onHttpkernelRouter(Event $event)
    {
        $result = $event->getFine()->getRouter->resolve($event->getRequest());

        $event->getRequest()->addAttributes($result->getParams());
        
        $event->setRouteResult($result);

        if ($result->hasClass()) {
            $event->setControllerClass($result->getClass());
        }
    }
    
    public function onHttpkernelGetController(Event $event)
    {
        $module = $event->getRouteResult()->getParam('module');
        $controller = $event->getRouteResult()->getParam('controller');
        $controllers = $event->fine->mod->app->controller;
        
        if (!isset($controllers->$module) || !isset($controllers->$module->$controller)) {
            throw new Http404NotFoundException();
        }
        
        $event->setController($controllers->$module->$controller);
    }
    
    public function onHttpkernelInjectServices(Event $event)
    {
        $controller = $event->getController();
        
        if ($controller instanceof HttpKernelEventAwareInterface) {
            $controller->setHttpkernelEvent($event);
        }
        
        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($event->getContainer());
        }
        
        if ($controller instanceof FineAwareInterface) {
            $controller->setFine($event->getFine());
        }
    }
    
    public function onHttpkernelDispatchController(Event $event)
    {
        $event->setResponse($event->getController()->dispatch($event->getRequest(), $event->getResponse()));
    }
    
}
