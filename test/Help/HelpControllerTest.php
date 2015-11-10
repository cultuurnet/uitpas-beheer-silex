<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class HelpControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HelpController
     */
    private $controller;

    /**
     * @var StorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storage;

    /**
     * @var AuthorizationCheckerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authorizationChecker;

    public function setUp()
    {
        $this->storage = $this->getMock(StorageInterface::class);
        $this->authorizationChecker = $this->getMock(
            AuthorizationCheckerInterface::class
        );
        $this->controller = new HelpController(
            $this->storage,
            $this->authorizationChecker
        );
    }

    public function canUpdateProvider()
    {
        return [
            [true, 'true'],
            [false, 'false'],
        ];
    }

    /**
     * @test
     * @dataProvider canUpdateProvider
     */
    public function it_loads_the_text_and_shows_update_permission($canUpdate, $canUpdateLiteralJson)
    {
        $text = new Text('Some *markdown* loaded');

        $this->authorizationChecker->expects($this->atLeastOnce())
            ->method('isGranted')
            ->with('ROLE_HELP_EDIT')
            ->willReturn($canUpdate);

        $this->storage->expects($this->once())
            ->method('load')
            ->willReturn($text);

        $response = $this->controller->get();

        $expectedResponseContent = '{"text": "Some *markdown* loaded", "canUpdate": ' . $canUpdateLiteralJson . '}';
        $this->assertJsonStringEqualsJsonString(
            $expectedResponseContent,
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function it_updates_the_text()
    {
        $request = $this->updateRequest();

        $this->authorizationChecker->expects($this->atLeastOnce())
            ->method('isGranted')
            ->with('ROLE_HELP_EDIT')
            ->willReturn(true);

        $this->storage->expects($this->once())
            ->method('save')
            ->with(new Text('Some *markdown*'));

        $response = $this->controller->update($request);

        $expectedResponseContent = '{"text": "Some *markdown*", "canUpdate": true}';

        $this->assertJsonStringEqualsJsonString(
            $expectedResponseContent,
            $response->getContent()
        );
    }

    /**
     * @return Request
     */
    private function updateRequest()
    {
        $content = '{"text": "Some *markdown*"}';
        $request = Request::create('/help', 'POST', [], [], [], [], $content);

        return $request;
    }

    /**
     * @test
     */
    public function it_denies_access_to_update_if_not_granted()
    {
        $this->setExpectedException(AccessDeniedHttpException::class);

        $this->authorizationChecker->expects($this->atLeastOnce())
            ->method('isGranted')
            ->with('ROLE_HELP_EDIT')
            ->willReturn(false);

        $this->storage->expects($this->never())
            ->method('save');

        $this->controller->update($this->updateRequest());
    }
}
