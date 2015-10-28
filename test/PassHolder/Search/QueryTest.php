<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\MembershipStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_build_a_passholder_query_with_some_sane_default_parameters()
    {
        $query = new Query();

        $expectedPassholderQuery = new \CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions();
        $expectedPassholderQuery->start = 0;
        $expectedPassholderQuery->includeBlocked = true;
        $expectedPassholderQuery->max = 10;

        $actualQuery = $query->build();

        $this->assertEquals($expectedPassholderQuery, $actualQuery);
    }

    /**
     * @test
     */
    public function it_should_build_a_passholder_query_with_custom_pagination_parameters()
    {
        $query = new Query();
        $query = $query->withPagination(new Integer(2), new Integer(42));

        $expectedPassholderQuery = new \CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions();
        // zero-based numbering for start offset
        $expectedPassholderQuery->start = 42;
        $expectedPassholderQuery->includeBlocked = true;
        $expectedPassholderQuery->max = 42;

        $actualQuery = $query->build();

        $this->assertEquals($expectedPassholderQuery, $actualQuery);
    }

    /**
     * @test
     */
    public function it_should_build_a_passholder_query_with_all_included_parameters()
    {
        $uitpasCollection =  new UiTPASNumberCollection();
        $uitpasCollection = $uitpasCollection->with(new UiTPASNumber('0930000816718'));
        $query = new Query();
        $query = $query
            ->withAssociationId(new AssociationId('wotm8'))
            ->withCity(new StringLiteral('Leuven'))
            ->withPagination(new Integer(2), new Integer(42))
            ->withEmail(new EmailAddress('dirk@e-dirk.de'))
            ->withFirstName(new StringLiteral('Dirk'))
            ->withName(new StringLiteral('Dirkington'))
            ->withUiTPASNumbers($uitpasCollection)
            ->withMembershipStatus(MembershipStatus::ACTIVE())
            ->withDateOfBirth(new Date(new Year(1980), Month::MAY(), new MonthDay(12)))
            ->withStreet(new StringLiteral('Dirklane'));

        $expectedPassholderQuery = new \CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions();
        // zero-based numbering for start offset
        $expectedPassholderQuery->start = 42;
        $expectedPassholderQuery->includeBlocked = true;
        $expectedPassholderQuery->max = 42;
        $expectedPassholderQuery->city = 'Leuven';
        $expectedPassholderQuery->email = 'dirk@e-dirk.de';
        $expectedPassholderQuery->name = 'Dirkington';
        $expectedPassholderQuery->firstName = 'Dirk';
        $expectedPassholderQuery->uitpasNumber = ['0930000816718'];
        $expectedPassholderQuery->associationId = 'wotm8';
        $expectedPassholderQuery->expiredMemberships = 'ACTIVE';
        $expectedPassholderQuery->dob = 326930400;
        $expectedPassholderQuery->street = 'Dirklane';


        $actualQuery = $query->build();

        $this->assertEquals($expectedPassholderQuery, $actualQuery);
    }
}
