<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

trait AdvantageAssertionTrait
{
    /**
     * @param Advantage $advantage
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param \ValueObjects\Number\Integer $points
     * @param $exchangeable
     */
    private function assertAdvantageData(
        Advantage $advantage,
        StringLiteral $id,
        StringLiteral $title,
        Integer $points,
        $exchangeable
    ) {
        $this->assertEquals($id, $advantage->getId());
        $this->assertEquals($title, $advantage->getTitle());
        $this->assertEquals($points, $advantage->getPoints());
        $this->assertEquals($exchangeable, $advantage->isExchangeable());
    }
}
