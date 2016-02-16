<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use ValueObjects\StringLiteral\StringLiteral;

class SchoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function always_has_an_id()
    {
        $id = new StringLiteral('school-unique-id');
        $school = new School($id);

        $this->assertSame($id, $school->getId());
    }

    /**
     * @test
     */
    public function can_have_a_name()
    {
        $id = new StringLiteral('school-unique-id');
        $name = new StringLiteral('Saint Whatever Institute');

        $school = new School($id, $name);

        $this->assertSame($name, $school->getName());
    }

    /**
     * @test
     */
    public function can_be_created_from_a_culturefeed_counter()
    {
        $cfCounter = new \CultureFeed_Uitpas_Counter();
        $cfCounter->name = 'Saint Whatever Institute';
        $cfCounter->consumerKey = 'school-unique-id';

        $expectedSchool = new School(
            new StringLiteral('school-unique-id'),
            new StringLiteral('Saint Whatever Institute')
        );

        $schoolCreatedFromCounter = School::fromCultureFeedCounter($cfCounter);

        $this->assertEquals(
            $expectedSchool,
            $schoolCreatedFromCounter
        );
    }
}
