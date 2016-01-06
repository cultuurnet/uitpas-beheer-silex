<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use ValueObjects\StringLiteral\StringLiteral;

class City extends StringLiteral implements \JsonSerializable
{
    public function __construct($value)
    {
        parent::__construct($value);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}
