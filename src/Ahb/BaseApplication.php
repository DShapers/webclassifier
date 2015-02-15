<?php

namespace Ahb;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;

class BaseApplication extends SilexApplication
{

    public function init()
    {
        $this->error(function (\Exception $e, $code) {
           return $this->json(array('message'=>$e->getMessage()));
        });
        //-- Define controllers
        $this->register(new \Silex\Provider\ServiceControllerServiceProvider());
        $this['entity.controller'] = $this->share(function() {
            return new \Ahb\Api\Controllers\Entity($this);
        });
        $this->before(function (Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            }
        });
        //-- Get main controller factory and map urls
        $ctrl = $this['controllers_factory'];
        $ctrl->get('/entity', 'entity.controller:get');
        $ctrl->post('/entity', 'entity.controller:post');
        //-- Mount controller factory on /api/v2 prefix
        $this->mount('/api/v2', $ctrl);
    }

}
