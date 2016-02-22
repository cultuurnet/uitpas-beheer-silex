<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolderDataTrait;
use CultuurNet\UiTPASBeheer\School\School;
use CultuurNet\UiTPASBeheer\School\SchoolServiceInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ValueObjects\StringLiteral\StringLiteral;

class PassHolderSchoolInfoDecoratedIdentityServiceTest extends PHPUnit_Framework_TestCase
{
    use PassHolderDataTrait;

    /**
     * @var IdentityServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $decoratee;

    /**
     * @var PassHolderSchoolInfoDecoratedIdentityService
     */
    private $decorator;

    /**
     * @var SchoolServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $schools;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->decoratee = $this->getMock(IdentityServiceInterface::class);

        $this->schools = $this->getMock(SchoolServiceInterface::class);

        $this->decorator = new PassHolderSchoolInfoDecoratedIdentityService(
            $this->decoratee,
            $this->schools
        );
    }

    /**
     * @test
     */
    public function passes_through_null_result_from_decoratee()
    {
        $identifier = '0930000343119';
        $this->decoratee->expects($this->once())
            ->method('get')
            ->with($identifier)
            ->willReturn(null);

        $result = $this->decorator->get($identifier);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function adds_the_school_name_to_the_passholder_school()
    {
        $identifier = '0930000343119';

        $schoolId = new StringLiteral('920f8d53-abd0-40f1-a151-960098197785');

        $identity = new Identity(
            new UiTPAS(
                new UiTPASNumber($identifier),
                UiTPASStatus::ACTIVE(),
                UiTPASType::CARD(),
                new CardSystem(
                    new CardSystemId('1'),
                    new StringLiteral('UiTPAS Dender')
                )
            )
        );

        $passHolderWithoutSchoolName = $this->getCompletePassHolder();
        $this->assertNull($passHolderWithoutSchoolName->getSchool()->getName());

        $identity = $identity->withPassHolder(
            $passHolderWithoutSchoolName
        );

        $this->decoratee->expects($this->once())
            ->method('get')
            ->with($identifier)
            ->willReturn(
                $identity
            );

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

        $expectedIdentity = $identity->withPassHolder(
            $passHolderWithSchoolName
        );

        $actualIdentity = $this->decorator->get($identifier);

        $this->assertEquals($expectedIdentity, $actualIdentity);
    }
}
