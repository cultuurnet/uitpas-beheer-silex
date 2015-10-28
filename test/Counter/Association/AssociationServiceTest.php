<?php

namespace CultuurNet\UiTPASBeheer\Counter\Association;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Membership\Association\AssociationCollection;

class AssociationServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var AssociationServiceInterface
     */
    protected $associationService;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('consumer-key');

        $this->associationService = new AssociationService($this->uitpas, $this->counterConsumerKey);
    }

    /**
     * @test
     */
    public function it_should_return_a_list_of_all_associations_for_the_active_counter()
    {
        $this->uitpas->expects($this->once())
            ->method('getAssociations')
            ->with($this->counterConsumerKey)
            ->willReturn(new \CultureFeed_ResultSet());

        $associations = $this->associationService->getAssociations();

        $this->assertInstanceOf(AssociationCollection::class, $associations);
    }

    /**
     * @test
     */
    public function it_should_return_a_list_of_associations_with_any_permission_for_the_active_counter()
    {
        $this->uitpas->expects($this->once())
            ->method('getAssociations')
            ->with($this->counterConsumerKey, false, false)
            ->willReturn(new \CultureFeed_ResultSet());

        $this->associationService->getAssociationsByPermission(Permission::ANY());
    }

    /**
     * @test
     */
    public function it_should_return_a_list_of_associations_with_read_permission_for_the_active_counter()
    {
        $this->uitpas->expects($this->once())
            ->method('getAssociations')
            ->with($this->counterConsumerKey, true, false)
            ->willReturn(new \CultureFeed_ResultSet());

        $this->associationService->getAssociationsByPermission(Permission::READ());
    }

    /**
     * @test
     */
    public function it_should_return_a_list_of_associations_with_registration_permission_for_the_active_counter()
    {
        $this->uitpas->expects($this->once())
            ->method('getAssociations')
            ->with($this->counterConsumerKey, false, true)
            ->willReturn(new \CultureFeed_ResultSet());

        $this->associationService->getAssociationsByPermission(Permission::REGISTER());
    }
}
