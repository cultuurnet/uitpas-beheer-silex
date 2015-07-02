<?php

namespace CultuurNet\UiTPASBeheer\Advantage\CashIn;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class CashIn
{
    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param StringLiteral $id
     */
    public function __construct(StringLiteral $id)
    {
        $this->id = $id;
    }

    /**
     * @return StringLiteral
     */
    public function getId()
    {
        return $this->id;
    }
}
