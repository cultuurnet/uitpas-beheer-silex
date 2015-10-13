<?php

namespace CultuurNet\UiTPASBeheer\Member;

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

        $controllers->get('/counter/active/members', 'member_controller:all');
        $controllers->post('/counter/active/members', 'member_controller:add');

        $controllers->delete('/counter/active/members/{uid}', 'member_controller:remove');

        return $controllers;
    }
}
