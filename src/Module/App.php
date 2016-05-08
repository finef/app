<?php

namespace Fine\App\Module;

use \Fine\Event\Event;
use \Fine\Event\EventDispatcher;

class App
{

    public function httpkernel(EventDispatcher $dispatcher)
    {
        $dispatcher
            ->on('app.httpkernel', [$this, 'onHttpkernelRouter'], 100)
            ->on('app.httpkernel', [$this, 'onHttpkernelControllerResolve'], 150)
            ->on('app.httpkernel', [$this, 'onHttpkernelCreateController'], 200)
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
    
    public function onHttpkernelControllerResolve(Event $event)
    {
        if ($event->hasControllerClass() || !$event->hasRouteResult()) {
            return;
        }
        
        $event->setControllerClass(
            $event->getFine()->getMod()->getApp()->getControllerResolver()->resolve(
                $event->getRouteResult()->getParam('module'), $event->getRouteResult()->getParam('controller')
            )
        );
    }
    
    public function onHttpkernelCreateController(Event $event)
    {
        if ($event->hasController()) {
            return;
        }
        
        $event->setController(new $event->getControllerClass());
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
