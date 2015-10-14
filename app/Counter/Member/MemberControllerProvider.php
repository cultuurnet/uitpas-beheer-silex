<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class MemberControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['member_controller'] = $app->share(
            function (Application $app) {
                return new MemberController(
                    $app['member_service'],
                    $app['user_service'],
                    new AddMemberJsonDeserializer()
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/counters/active/members', 'member_controller:all');
        $controllers->post('/counters/active/members', 'member_controller:add');

        $controllers->delete('/counters/active/members/{uid}', 'member_controller:remove');

        return $controllers;
    }
}
