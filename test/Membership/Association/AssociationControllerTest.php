<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;

class AssociationControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var AssociationServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var AssociationController
     */
    protected $controller;

    public function setUp()
    {
        $this->service = $this->getMock(AssociationServiceInterface::class);
        $this->controller = new AssociationController($this->service);
    }

    /**
     * @test
     */
    public function it_responds_a_list_of_all_associations_the_active_counter_can_register_for()
    {
        $associationA = new \CultureFeed_Uitpas_Association();
        $associationA->id = 5;
        $associationA->name = 'Foo';

        $associationB = new \CultureFeed_Uitpas_Association();
        $associationB->id = 3;
        $associationB->name = 'Bar';

        $associations = array(
            $associationA,
            $associationB,
        );

        $this->service->expects($this->once())
            ->method('getAssociationsByPermission')
            ->with(Permission::REGISTER())
            ->willReturn($associations);

        $response = $this->controller->getAssociations();

        $this->assertContains('private', $response->headers->get('Cache-Control'));
        $this->assertNotContains('public', $response->headers->get('Cache-Control'));

        $this->assertJsonEquals(
            $response->getContent(),
            'Membership/Association/data/association-collection-array.json'
        );
    }
}
