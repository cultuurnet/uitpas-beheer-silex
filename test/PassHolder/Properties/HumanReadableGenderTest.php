<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\Properties\Language;

class HumanReadableGenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Gender
     */
    private $man;

    /**
     * @var Gender
     */
    private $woman;

    public function setUp()
    {
        $this->man = Gender::MALE();
        $this->woman = Gender::FEMALE();
    }

    /**
     * @test
     */
    public function it_can_return_the_human_readable_gender_in_the_correct_language()
    {
        // EN.
        $expectedManEn = 'Man';
        $manEn = new HumanReadableGender($this->man, Language::EN());

        $expectedWomanEn = 'Woman';
        $womanEn = new HumanReadableGender($this->woman, Language::EN());

        $this->assertEquals($expectedManEn, $manEn->toNative());
        $this->assertEquals($expectedWomanEn, $womanEn->toNative());

        // NL.
        $expectedManNl = 'Man';
        $manNl = new HumanReadableGender($this->man, Language::NL());

        $expectedWomanNl = 'Vrouw';
        $womanNl = new HumanReadableGender($this->woman, Language::NL());

        $this->assertEquals($expectedManNl, $manNl->toNative());
        $this->assertEquals($expectedWomanNl, $womanNl->toNative());
    }
}
