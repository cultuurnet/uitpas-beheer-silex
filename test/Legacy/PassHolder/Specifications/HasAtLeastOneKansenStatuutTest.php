<?php

namespace CultuurNet\UiTPASBeheer\Legacy\PassHolder\Specifications;

use CultuurNet\UiTPASBeheer\Legacy\PassHolder\Specifications\HasAtLeastOneExpiredKansenStatuut;

class HasAtLeastOneExpiredKansenStatuutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CultureFeed_Uitpas_PassHolder
     */
    protected $cfPassHolder;

    /**
     * @var \CultureFeed_Uitpas_CardSystem
     */
    protected $activeKansenStatuutCardSystem;

    /**
     * @var \CultureFeed_Uitpas_CardSystem
     */
    protected $expiredKansenStatuutCardSystem;

    /**
     * @var \CultureFeed_Uitpas_CardSystem
     */
    protected $nonKansenStatuutCardSystem;

    public function setUp()
    {
        $this->cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $this->cfPassHolder->firstName = 'Foo';
        $this->cfPassHolder->name = 'Bar';
        $this->cfPassHolder->email = 'foo@bar.com';

        $this->activeKansenStatuutCardSystem = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $this->activeKansenStatuutCardSystem->kansenStatuut = true;
        $this->activeKansenStatuutCardSystem->kansenStatuutExpired = false;
        $this->activeKansenStatuutCardSystem->kansenStatuutEndDate = 1441097191;
        $this->activeKansenStatuutCardSystem->kansenStatuutInGracePeriod = false;

        $this->expiredKansenStatuutCardSystem = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $this->expiredKansenStatuutCardSystem->kansenStatuut = true;
        $this->expiredKansenStatuutCardSystem->kansenStatuutExpired = true;
        $this->expiredKansenStatuutCardSystem->kansenStatuutEndDate = 1438387200;
        $this->expiredKansenStatuutCardSystem->kansenStatuutInGracePeriod = false;

        $this->nonKansenStatuutCardSystem = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $this->nonKansenStatuutCardSystem->kansenStatuut = false;
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_a_passholder_with_no_kansenstatuut_cards()
    {
        $this->cfPassHolder->cardSystemSpecific[] = $this->nonKansenStatuutCardSystem;

        $this->assertFalse(
            HasAtLeastOneExpiredKansenStatuut::isSatisfiedBy($this->cfPassHolder)
        );
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_a_passholder_with_only_active_kansenstatuut_cards()
    {
        $this->cfPassHolder->cardSystemSpecific[] = $this->nonKansenStatuutCardSystem;
        $this->cfPassHolder->cardSystemSpecific[] = $this->activeKansenStatuutCardSystem;

        $this->assertFalse(
            HasAtLeastOneExpiredKansenStatuut::isSatisfiedBy($this->cfPassHolder)
        );
    }

    /**
     * @test
     */
    public function it_is_satisfied_by_a_passholder_with_one_expired_kansenstatuut_card()
    {
        // Put the expired card in the middle so we can make sure that adding
        // an active card at the end doesn't confuse the specification.
        $this->cfPassHolder->cardSystemSpecific[] = $this->nonKansenStatuutCardSystem;
        $this->cfPassHolder->cardSystemSpecific[] = $this->expiredKansenStatuutCardSystem;
        $this->cfPassHolder->cardSystemSpecific[] = $this->activeKansenStatuutCardSystem;

        $this->assertTrue(
            HasAtLeastOneExpiredKansenStatuut::isSatisfiedBy($this->cfPassHolder)
        );
    }

    /**
     * @test
     */
    public function it_is_satisfied_by_a_passholder_with_multiple_expired_kansenstatuut_cards()
    {
        $this->cfPassHolder->cardSystemSpecific[] = $this->expiredKansenStatuutCardSystem;
        $this->cfPassHolder->cardSystemSpecific[] = $this->nonKansenStatuutCardSystem;
        $this->cfPassHolder->cardSystemSpecific[] = $this->expiredKansenStatuutCardSystem;
        $this->cfPassHolder->cardSystemSpecific[] = $this->activeKansenStatuutCardSystem;

        $this->assertTrue(
            HasAtLeastOneExpiredKansenStatuut::isSatisfiedBy($this->cfPassHolder)
        );
    }
}
