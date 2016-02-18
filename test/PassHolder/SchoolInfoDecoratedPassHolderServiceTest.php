<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\School\School;
use CultuurNet\UiTPASBeheer\School\SchoolServiceInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ValueObjects\StringLiteral\StringLiteral;

class SchoolInfoDecoratedPassHolderServiceTest extends PHPUnit_Framework_TestCase
{
    use PassHolderDataTrait;

    /**
     * @var SchoolInfoDecoratedPassHolderService
     */
    private $decorator;

    /**
     * @var PassHolderServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $decoratee;

    /**
     * @var SchoolServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $schools;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->decoratee = $this->getMock(PassHolderServiceInterface::class);

        $this->schools = $this->getMock(SchoolServiceInterface::class);

        $this->decorator = new SchoolInfoDecoratedPassHolderService(
            $this->decoratee,
            $this->schools
        );
    }

    /**
     * @test
     */
    public function passes_through_null_result_from_decoratee()
    {
        $uitpasNumber = new UiTPASNumber('0930000343119');
        $this->decoratee->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($uitpasNumber)
            ->willReturn(null);

        $result = $this->decorator->getByUitpasNumber($uitpasNumber);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function adds_the_school_name_to_the_passholder_school()
    {
        $uitpasNumber = new UiTPASNumber('0930000343119');

        $schoolId = new StringLiteral('920f8d53-abd0-40f1-a151-960098197785');

        $passHolderWithoutSchoolName = $this->getCompletePassHolder();
        $this->assertNull($passHolderWithoutSchoolName->getSchool()->getName());

        $this->decoratee->expects($this->once())
            ->method('getByUitpasNumber')
            ->with($uitpasNumber)
            ->willReturn($passHolderWithoutSchoolName);

        $schoolEnhancedWithName = new School(
            $schoolId,
            new StringLiteral('University of Life')
        );

        $this->schools->expects($this->once())
            ->method('get')
            ->with($this->equalTo($schoolId))
            ->willReturn($schoolEnhancedWithName);

        $passHolderWithSchoolName = $passHolderWithoutSchoolName->withSchool(
            $schoolEnhancedWithName
        );

        $actualPassHolder = $this->decorator->getByUitpasNumber($uitpasNumber);

        $this->assertEquals($passHolderWithSchoolName, $actualPassHolder);
    }
}
