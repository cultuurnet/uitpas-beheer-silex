<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\properties;

use ValueObjects\StringLiteral\StringLiteral;

class VoucherType implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $name;

    /**
     * @var StringLiteral
     */
    protected $prefix;

    /**
     * @param StringLiteral $name
     * @param StringLiteral $prefix
     */
    public function __construct(
        StringLiteral $name,
        StringLiteral $prefix
    ) {
        $this->name = $name;
        $this->prefix = $prefix;
    }

    /**
     * @return StringLiteral
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return StringLiteral
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $jsonData = [
            "name" => $this->getName()->toNative(),
            "prefix" => $this->getPrefix()->toNative(),
        ];

        return $jsonData;
    }
}
