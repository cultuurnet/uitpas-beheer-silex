<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use ValueObjects\StringLiteral\StringLiteral;

class AdvantageIdentifierTest extends \PHPUnit_Framework_TestCase
{
    const VALID_WELCOME_ADVANTAGE_IDENTIFIER = 'welcome--10';
    const VALID_POINTS_PROMOTION_ADVANTAGE_IDENTIFIER = 'points-promotion--10';

    const INVALID_SEPARATOR_IDENTIFIER = 'welcome-advantage-10';
    const INVALID_SEPARATOR_AMOUNT_IDENTIFIER = 'welcome--advantage--10';
    const INVALID_TYPE_IDENTIFIER = 'unknown--10';

    /**
     * @test
     * @dataProvider validAdvantageIdentifiers
     *
     * @param AdvantageIdentifier $identifier
     * @param AdvantageType $expectedType
     * @param StringLiteral $expectedId
     */
    public function it_can_determine_the_advantage_type_and_id_and_return_them(
        AdvantageIdentifier $identifier,
        AdvantageType $expectedType,
        StringLiteral $expectedId
    ) {
        $this->assertEquals($expectedType, $identifier->getType());
        $this->assertEquals($expectedId, $identifier->getId());
    }

    /**
     * @test
     * @dataProvider validAdvantageIdentifiers
     *
     * @param AdvantageIdentifier $expectedIdentifier
     * @param AdvantageType $type
     * @param StringLiteral $id
     */
    public function it_can_create_a_new_instance_from_an_advantage_type_and_id_objects(
        AdvantageIdentifier $expectedIdentifier,
        AdvantageType $type,
        StringLiteral $id
    ) {
        $identifier = AdvantageIdentifier::fromAdvantageTypeAndId($type, $id);
        $this->assertTrue($expectedIdentifier->sameValueAs($identifier));
    }

    /**
     * @return array
     */
    public function validAdvantageIdentifiers()
    {
        return [
            [
                new AdvantageIdentifier(self::VALID_WELCOME_ADVANTAGE_IDENTIFIER),
                AdvantageType::WELCOME(),
                new StringLiteral('10'),
            ],
            [
                new AdvantageIdentifier(self::VALID_POINTS_PROMOTION_ADVANTAGE_IDENTIFIER),
                AdvantageType::POINTS_PROMOTION(),
                new StringLiteral('10'),
            ],
        ];
    }

    /**
     * @test
     */
    public function it_validates_the_presence_of_a_separator()
    {
        $this->setExpectedException(
            AdvantageIdentifierInvalidException::class,
            'Advantage identifier should contain exactly one separator (--).'
        );
        new AdvantageIdentifier(self::INVALID_SEPARATOR_IDENTIFIER);
    }

    /**
     * @test
     */
    public function it_validates_the_amount_of_separators()
    {
        $this->setExpectedException(
            AdvantageIdentifierInvalidException::class,
            'Advantage identifier should contain exactly one separator (--).'
        );
        new AdvantageIdentifier(self::INVALID_SEPARATOR_AMOUNT_IDENTIFIER);
    }

    /**
     * @test
     */
    public function it_validates_the_advantage_type()
    {
        $this->setExpectedException(
            AdvantageIdentifierInvalidException::class,
            'Invalid advantage type found in advantage identifier.'
        );
        new AdvantageIdentifier(self::INVALID_TYPE_IDENTIFIER);
    }
}
