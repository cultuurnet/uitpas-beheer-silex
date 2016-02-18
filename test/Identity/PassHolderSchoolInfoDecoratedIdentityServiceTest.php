<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Identity;

use PHPUnit_Framework_TestCase;

class PassHolderSchoolInfoDecoratedIdentityServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var IdentityServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $decoratee;

    /**
     * @var PassHolderSchoolInfoDecoratedIdentityService
     */
    private $decorater;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->decoratee = $this->getMock(IdentityServiceInterface::class);

        $this->decorater = new PassHolderSchoolInfoDecoratedIdentityService(
            $this->decoratee
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

        $result = $this->decorater->get($identifier);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function enhances_the_school_in_the_passholder_with_its_name()
    {

    }
}
