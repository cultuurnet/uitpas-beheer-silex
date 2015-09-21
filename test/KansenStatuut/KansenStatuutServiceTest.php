<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultureFeed_Uitpas;
use CultureFeed_Uitpas_Passholder;
use CultureFeed_Uitpas_Passholder_UitIdUser;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;

class KansenStatuutServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CultureFeed_Uitpas|PHPUnit_Framework_MockObject_MockObject
     */
    private $uitpasService;

    /**
     * @var KansenStatuutService
     */
    private $kansenStatuutService;

    /**
     * @var CultureFeed_Uitpas_Passholder
     */
    private $passHolder;

    /**
     * @var CounterConsumerKey
     */
    private $counterConsumerKey;

    public function setUp()
    {
        $this->uitpasService = $this->getMock(CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('foo-123');

        $this->kansenStatuutService = new KansenStatuutService(
            $this->uitpasService,
            $this->counterConsumerKey
        );

        $passHolder = new CultureFeed_Uitpas_Passholder();
        $passHolder->uitIdUser = new CultureFeed_Uitpas_Passholder_UitIdUser();
        $passHolder->uitIdUser->id = '5';
        $passHolder->uitIdUser->name = 'John Doe';
        $this->passHolder = $passHolder;

        $this->uitpasService->expects($this->any())
            ->method('getPassholderByUitpasNumber')
            ->with('0930000807113')
            ->willReturn(
                $this->passHolder
            );
    }

    /**
     * @test
     */
    public function it_can_renew_a_kansen_statuut()
    {
        $this->uitpasService->expects($this->once())
            ->method('updatePassholderCardSystemPreferences')
            ->with(
                $this->callback(
                    function (\CultureFeed_Uitpas_Passholder_CardSystemPreferences $preferences) {
                        return $preferences->toPostData() === [
                            'id' => '5',
                            'cardSystemId' => '2',
                            'kansenStatuutEndDate' => '2016-06-15',
                            'balieConsumerKey' => 'foo-123',
                        ];
                    }
                )
            );

        $this->kansenStatuutService->renew(
            new UiTPASNumber('0930000807113'),
            new CardSystemId('2'),
            new Date(
                new Year(2016),
                Month::JUNE(),
                new MonthDay(15)
            )
        );
    }

    /**
     * @test
     */
    public function it_transforms_culturefeed_exceptions_to_readable_code_exceptions()
    {
        $this->setExpectedException(ReadableCodeResponseException::class);

        $exception = new \CultureFeed_Exception(
            'Passholder not registered in the given card system',
            'ACTION_NOT_ALLOWED'
        );

        $this->uitpasService->expects($this->once())
            ->method('updatePassholderCardSystemPreferences')
            ->willThrowException($exception);

        try {
            $this->kansenStatuutService->renew(
                new UiTPASNumber('0930000807113'),
                new CardSystemId('2'),
                new Date(
                    new Year(2016),
                    Month::JUNE(),
                    new MonthDay(15)
                )
            );
        } catch (ReadableCodeResponseException $e) {
            $this->assertEquals(
                'ACTION_NOT_ALLOWED',
                $e->getReadableCode()
            );

            throw $e;
        }
    }
}
