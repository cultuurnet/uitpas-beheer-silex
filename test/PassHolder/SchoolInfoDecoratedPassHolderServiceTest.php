<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\School\SchoolServiceInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class SchoolInfoDecoratedPassHolderServiceTest extends PHPUnit_Framework_TestCase
{
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
}
