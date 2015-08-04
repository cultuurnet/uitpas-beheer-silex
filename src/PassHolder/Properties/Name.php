<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\StringLiteral\StringLiteral;

final class Name implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $first;

    /**
     * @var StringLiteral|null
     */
    protected $middle;

    /**
     * @var StringLiteral
     */
    protected $last;

    /**
     * @param StringLiteral $first
     * @param StringLiteral $last
     */
    public function __construct(
        StringLiteral $first,
        StringLiteral $last
    ) {
        $this->first = $first;
        $this->last = $last;
    }

    /**
     * @return StringLiteral
     */
    public function getFirstName()
    {
        return $this->first;
    }

    /**
     * @return StringLiteral
     */
    public function getLastName()
    {
        return $this->last;
    }

    /**
     * @param StringLiteral $middle
     * @return Name
     */
    public function withMiddleName(StringLiteral $middle)
    {
        $c = clone $this;
        $c->middle = $middle;
        return $c;
    }

    /**
     * @return StringLiteral|null
     */
    public function getMiddleName()
    {
        return $this->middle;
    }

    /**
     * @param Name $other
     * @return bool
     */
    public function sameValueAs(Name $other)
    {
        return $this->jsonSerialize() === $other->jsonSerialize();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data['first'] = $this->first->toNative();

        if (!is_null($this->middle)) {
            $data['middle'] = $this->middle->toNative();
        }

        $data['last'] = $this->last->toNative();
        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder $cfPassHolder
     * @return self
     */
    public static function fromCultureFeedPassHolder(\CultureFeed_Uitpas_Passholder $cfPassHolder)
    {
        // Even though first and last name are required, there are some passholders that do not have those. We just
        // use an empty string in those cases.
        $firstName = !is_null($cfPassHolder->firstName) ? (string) $cfPassHolder->firstName : '';
        $firstName = new StringLiteral($firstName);

        $lastName = !is_null($cfPassHolder->name) ? (string) $cfPassHolder->name : '';
        $lastName = new StringLiteral($lastName);

        $name = new self($firstName, $lastName);

        if (!is_null($cfPassHolder->secondName)) {
            $name = $name->withMiddleName(new StringLiteral($cfPassHolder->secondName));
        }

        return $name;
    }
}
