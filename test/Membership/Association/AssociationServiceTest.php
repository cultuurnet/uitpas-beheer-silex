<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;

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

        $resultSet = new \CultureFeed_ResultSet(2, $associations);

        $this->uitpas->expects($this->once())
            ->method('getAssociations')
            ->with($this->counterConsumerKey->toNative())
            ->willReturn($resultSet);

        $this->assertEquals(
            new AssociationCollection($associations),
            $this->associationService->getAssociations()
        );
    }

    /**
     * @test
     */
    public function it_should_return_a_list_of_associations_with_any_permission_for_the_active_counter()
    {
        $this->uitpas->expects($this->once())
            ->method('getAssociations')
            ->with($this->counterConsumerKey, $this->identicalTo(null), $this->identicalTo(null))
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
            ->with($this->counterConsumerKey, true, $this->identicalTo(null))
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
            ->with($this->counterConsumerKey, $this->identicalTo(null), true)
            ->willReturn(new \CultureFeed_ResultSet());

        $this->associationService->getAssociationsByPermission(Permission::REGISTER());
    }
}
