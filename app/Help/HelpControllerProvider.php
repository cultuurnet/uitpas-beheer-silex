<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class HelpControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $app['help_controller'] = $app->share(
            function () {
                return new HelpController(
                    new FileStorage(__DIR__ . '/../../var/help.md')
                );
            }
        );

        $userIsGrantedHelpEditRole = function (Request $request, Application $app) {
            /** @var AuthorizationCheckerInterface $authorizationChecker */
            $authorizationChecker = $app['security.authorization_checker'];
            if (!$authorizationChecker->isGranted('ROLE_HELP_EDIT')) {
                throw new AccessDeniedHttpException();
            }
        };

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/help', 'help_controller:get');
        $controllers->post('/help', 'help_controller:update')
            ->before($userIsGrantedHelpEditRole);

        return $controllers;
    }
}
