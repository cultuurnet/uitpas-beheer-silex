<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use CultureFeed_ResultSet;
use CultureFeed_Uitpas;
use CultureFeed_Uitpas_Counter;
use CultureFeed_Uitpas_Counter_Employee;
use CultureFeed_Uitpas_Counter_EmployeeCardSystem;
use CultureFeed_Uitpas_Counter_Query_SearchCounterOptions;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Counter\CounterServiceInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ValueObjects\StringLiteral\StringLiteral;

class SchoolServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CounterServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $counterService;

    /**
     * @var SchoolService
     */
    protected $schoolService;

    /**
     * @var CultureFeed_Uitpas|PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpasService;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->uitpasService = $this->getMock(CultureFeed_Uitpas::class);
        $this->counterService = $this->getMock(CounterServiceInterface::class);

        $this->counterConsumerKey = new CounterConsumerKey('123-456-789-101');

        $this->schoolService = new SchoolService(
            $this->uitpasService,
            $this->counterConsumerKey,
            $this->counterService
        );
    }

    /**
     * @test
     */
    public function retrieves_schools_in_the_cardsystems_of_the_active_counter()
    {
        $cardSystem4 = new CultureFeed_Uitpas_Counter_EmployeeCardSystem();
        $cardSystem4->id = 4;

        $cardSystem7 = new CultureFeed_Uitpas_Counter_EmployeeCardSystem();
        $cardSystem7->id = 7;

        $activeCounter = new CultureFeed_Uitpas_Counter_Employee();
        $activeCounter->cardSystems = [$cardSystem4, $cardSystem7];

        $cfSchoolCounterA = new CultureFeed_Uitpas_Counter();
        $cfSchoolCounterA->consumerKey = 'school-unique-id-A';
        $cfSchoolCounterA->name = 'School A';

        $cfSchoolCounterB = new CultureFeed_Uitpas_Counter();
        $cfSchoolCounterB->consumerKey = 'school-unique-id-B';
        $cfSchoolCounterB->name = 'School B';

        $this->counterService->expects($this->once())
            ->method('getActiveCounter')
            ->willReturn(
                $activeCounter
            );

        $expectedCounterSearchConditions = new CultureFeed_Uitpas_Counter_Query_SearchCounterOptions();
        $expectedCounterSearchConditions->cardSystemId = [4, 7];
        $expectedCounterSearchConditions->school = true;
        $expectedCounterSearchConditions->start = 0;
        $expectedCounterSearchConditions->max = 1000;

        $this->uitpasService->expects($this->once())
            ->method('searchCounters')
            ->with($expectedCounterSearchConditions)
            ->willReturn(
                new CultureFeed_ResultSet(
                    2,
                    [$cfSchoolCounterA, $cfSchoolCounterB]
                )
            );

        $expectedSchools = SchoolCollection::fromArray(
            [
                new School(
                    new StringLiteral('school-unique-id-A'),
                    new StringLiteral('School A')
                ),
                new School(
                    new StringLiteral('school-unique-id-B'),
                    new StringLiteral('School B')
                ),
            ]
        );

        $schools = $this->schoolService->getSchools();

        $this->assertEquals(
            $expectedSchools,
            $schools
        );
    }
}
