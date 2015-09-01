<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

class UnregisteredAssociationFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CultureFeed_Uitpas_Association[]
     */
    protected $unfilteredAssociations;

    /**
     * @var AssociationCollection
     */
    protected $unfilteredAssociationsCollection;

    /**
     * @var \CultureFeed_Uitpas_Association[]
     */
    protected $passHolderAssociations;

    /**
     * @var \CultureFeed_Uitpas_PassHolder
     */
    protected $cfPassHolder;

    /**
     * @var AssociationFilterInterface
     */
    protected $filter;

    public function setUp()
    {
        $associationA = new \CultureFeed_Uitpas_Association();
        $associationA->id = 5;
        $associationA->name = 'Foo';

        $associationB = new \CultureFeed_Uitpas_Association();
        $associationB->id = 3;
        $associationB->name = 'Bar';

        $associationC = new \CultureFeed_Uitpas_Association();
        $associationC->id = 11;
        $associationC->name = 'Lorem';

        $associationD = new \CultureFeed_Uitpas_Association();
        $associationD->id = 15;
        $associationD->name = 'Ipsum';

        $this->unfilteredAssociations = array(
            5 => $associationA,
            3 => $associationB,
            11 => $associationC,
        );

        $this->unfilteredAssociationsCollection = new AssociationCollection(
            $this->unfilteredAssociations
        );

        $this->passHolderAssociations = array(
            3 => $associationB,
            11 => $associationC,
            15 => $associationD,
        );

        $this->cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $this->cfPassHolder->memberships = array();

        foreach ($this->passHolderAssociations as $passHolderAssociation) {
            $membership = new \CultureFeed_Uitpas_Passholder_Membership();
            $membership->association = $passHolderAssociation;
            $this->cfPassHolder->memberships[] = $membership;
        }

        $this->filter = new UnregisteredAssociationFilter($this->cfPassHolder);
    }

    /**
     * @test
     */
    public function it_can_filter_out_any_associations_that_a_culturefeed_passholder_has_a_membership_for()
    {
        $expected = array(5 => $this->unfilteredAssociations[5]);

        $actual = $this->filter->filter($this->unfilteredAssociationsCollection)
            ->getAssociationMap();

        $this->assertEquals($expected, $actual);
    }
}
