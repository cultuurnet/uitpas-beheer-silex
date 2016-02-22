<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutJsonDeserializer;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class CardSystemUpgradeJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CardSystemUpgradeJsonDeserializer
     */
    protected $deserializer;

    /**
     * @var KansenStatuutJsonDeserializer
     */
    protected $kansenStatuutDeserializer;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->kansenStatuutDeserializer = new KansenStatuutJsonDeserializer();

        $this->deserializer = new CardSystemUpgradeJsonDeserializer(
            $this->kansenStatuutDeserializer
        );
    }

    public function deserializationDataProvider()
    {
        $voucherNumber = new VoucherNumber('free ticket to ride');

        return [
            'without new uitpas' => [
                new StringLiteral(
                    $this->getCardSystemUpgradeSample(false, false)
                ),
                CardSystemUpgrade::withoutNewUiTPAS(
                    new CardSystemId('1')
                )->withVoucherNumber(
                    $voucherNumber
                ),
            ],
            'with new uitpas and kansenstatuut' => [
                new StringLiteral(
                    $this->getCardSystemUpgradeSample(false, true)
                ),
                CardSystemUpgrade::withNewUiTPAS(
                    new UiTPASNumber('0930000125607'),
                    new KansenStatuut(
                        Date::fromNativeDateTime(new \DateTime('2016-02-03'))
                    )
                )->withVoucherNumber(
                    $voucherNumber
                ),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider deserializationDataProvider
     */
    public function deserializes_all_properties(StringLiteral $sample, CardSystemUpgrade $expectedCardSystemUpgrade)
    {
        $actualCardSystemUpgrade = $this->deserializer->deserialize($sample);

        $this->assertEquals(
            $expectedCardSystemUpgrade,
            $actualCardSystemUpgrade
        );
    }

    /**
     * @test
     */
    public function refuses_to_deserialize_when_neither_a_cardSystemId_and_uitpasNumber_are_present()
    {
        $this->setExpectedException(MissingPropertyException::class);

        $sample = $this->getCardSystemUpgradeSample();
        unset($sample->cardSystemId);

        $this->deserializer->deserialize(
            new StringLiteral(
                json_encode($sample)
            )
        );
    }

    /**
     * @param bool $decoded
     * @return stdClass|string
     */
    private function getCardSystemUpgradeSample($decoded = true, $withNewUiTPAS = false)
    {
        if ($withNewUiTPAS) {
            $file = 'cardsystem-upgrade-with-new-uitpas.json';
        } else {
            $file = 'cardsystem-upgrade-without-new-uitpas.json';
        }

        $json = file_get_contents(__DIR__ . '/data/' . $file);

        if ($decoded) {
            return json_decode($json);
        }

        return $json;
    }
}
