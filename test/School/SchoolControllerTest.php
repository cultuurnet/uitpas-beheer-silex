<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ValueObjects\StringLiteral\StringLiteral;

class SchoolControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SchoolController
     */
    private $controller;

    /**
     * @var SchoolServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $schoolService;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->schoolService = $this->getMock(SchoolServiceInterface::class);

        $this->controller = new SchoolController($this->schoolService);
    }

    /**
     * @test
     */
    public function returns_json_serialized_collection_of_schools()
    {
        $schoolA = new School(
            new StringLiteral('unique-id-A'),
            new StringLiteral('School A')
        );

        $schoolB = new School(
            new StringLiteral('unique-id-B'),
            new StringLiteral('School B')
        );

        $schools = SchoolCollection::fromArray([$schoolA, $schoolB]);

        $this->schoolService->expects($this->once())
            ->method('getSchools')
            ->willReturn($schools);

        $response = $this->controller->getSchools();

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/data/schools.json',
            $response->getContent()
        );

        $this->assertSame(
            'application/json',
            $response->headers->get('Content-Type')
        );
    }
}
