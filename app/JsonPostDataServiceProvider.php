<?php

namespace CultuurNet\UiTPASBeheer;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class JsonPostDataServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        /**
         * @see http://silex.sensiolabs.org/doc/cookbook/json_request_body.html#parsing-the-request-body
         */
        $app->before(function (Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            }
        });
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
