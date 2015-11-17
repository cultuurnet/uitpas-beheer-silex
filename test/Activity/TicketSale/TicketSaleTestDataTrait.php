<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

trait TicketSaleTestDataTrait
{
    /**
     * @return TicketSale[]
     */
    public function getTicketSaleHistory()
    {
        return [
            new TicketSale(
                new StringLiteral('aaa'),
                new Real(1.5),
                DateTime::fromNativeDateTime(new \DateTime('@1447174206')),
                new StringLiteral('Lorem Ipsum')
            ),
            new TicketSale(
                new StringLiteral('bbb'),
                new Real(2.0),
                DateTime::fromNativeDateTime(new \DateTime('@0')),
                new StringLiteral('Dolor Sit Amet')
            ),
        ];
    }
}
