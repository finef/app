<?php

namespace App\Module;

use \Fine\Event;

require __DIR__ . '/global.php';

class Module extends \Fine\Application\ModuleAbstract
{

    public function register($app)
    {

        $app(array(

            'db' =>  function() use ($app) {
                $target = $app->target->current;
                $app->db = \Fine\Db\MySQL\Client::newInstace();
                $app->module->each()->app->db();
                return $app->db;
            },

            'config' => function() use ($app) {
                return $app->config = \Fine\Config\Config::newInstace()->path('module/App/config');
            },

            'dispatcher' => function() use ($app) {
                /** @todo */
            },

            'request' => function() use ($app) {
                return $app->request = \Fine\Controller\Request::newFromGlobals();
            },

            'response' => function() use ($app) {
                return $app->response = new \Fine\Controller\Response();
            },

            'router' => function() use ($app) {
            },

        ));

        $app->event->on('application.bootstrap', array($this, 'bootstrap'));

    }

    public function bootstrap(Event $event)
    {


        // $request = Request::capture();
        //
        // $response = $this->kernel->handle();


    }

}
