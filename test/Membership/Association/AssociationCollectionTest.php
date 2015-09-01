<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;

class AssociationCollectionTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var \CultureFeed_Uitpas_Association[]
     */
    protected $associations;

    public function setUp()
    {
        $this->associations = array();

        $associationA = new \CultureFeed_Uitpas_Association();
        $associationA->id = 5;
        $associationA->name = 'Foo';
        $this->associations[5] = $associationA;

        $associationB = new \CultureFeed_Uitpas_Association();
        $associationB->id = 3;
        $associationB->name = 'Bar';
        $this->associations[3] = $associationB;
    }

    /**
     * @test
     */
    public function it_can_be_initialized_from_an_array_of_associations()
    {
        // Use an array without ids as keys, so we can test that the
        // associations are automatically mapped to their ids.
        $associationCollection = new AssociationCollection(
            array_values($this->associations)
        );

        $this->assertEquals(
            $this->associations,
            $associationCollection->getAssociationMap()
        );
    }

    /**
     * @test
     */
    public function it_guards_the_object_type_of_array_values_in_the_constructor()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        new AssociationCollection(
            array(new \stdClass())
        );
    }

    /**
     * @test
     */
    public function it_can_add_more_associations_after_initialization()
    {
        $associationCollection = new AssociationCollection(
            array($this->associations[5])
        );

        $associationCollection = $associationCollection->withAssociation(
            $this->associations[3]
        );

        $this->assertEquals(
            $this->associations,
            $associationCollection->getAssociationMap()
        );
    }

    /**
     * @test
     */
    public function it_can_remove_associations_after_initialization()
    {
        $associationCollection = new AssociationCollection(
            $this->associations
        );

        $associationCollection = $associationCollection->withoutAssociation(
            $this->associations[5]
        );

        $this->assertEquals(
            array(3 => $this->associations[3]),
            $associationCollection->getAssociationMap()
        );
    }

    /**
     * @test
     */
    public function it_can_handle_removing_associations_that_are_not_set()
    {
        $associationCollection = new AssociationCollection(
            array(3 => $this->associations[3])
        );

        $associationCollection = $associationCollection->withoutAssociation(
            $this->associations[5]
        );

        $this->assertEquals(
            array(3 => $this->associations[3]),
            $associationCollection->getAssociationMap()
        );
    }

    /**
     * @test
     */
    public function it_can_encode_to_json()
    {
        $associationCollection = new AssociationCollection(
            $this->associations
        );

        $json = json_encode($associationCollection);
        $this->assertJsonEquals($json, 'Membership/Association/data/association-collection.json');
    }
}
