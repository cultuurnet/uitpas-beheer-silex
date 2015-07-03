<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use ValueObjects\StringLiteral\StringLiteral;

class AdvantageIdentifier extends StringLiteral
{
    const SEPARATOR = '--';

    /**
     * @var AdvantageType
     */
    protected $type;

    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct($value);

        $parts = explode(self::SEPARATOR, $this->value);

        if (count($parts) !== 2) {
            $message = sprintf('Advantage identifier should contain exactly one separator (%s).', self::SEPARATOR);
            throw new AdvantageIdentifierInvalidException($message);
        }

        try {
            $this->type = AdvantageType::get($parts[0]);
        } catch (\InvalidArgumentException $exception) {
            throw new AdvantageIdentifierInvalidException('Invalid advantage type found in advantage identifier.');
        }

        $this->id = StringLiteral::fromNative($parts[1]);
    }

    /**
     * @return AdvantageType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return StringLiteral
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param AdvantageType $type
     * @param StringLiteral $id
     *
     * @return static
     */
    public static function fromAdvantageTypeAndId(AdvantageType $type, StringLiteral $id)
    {
        return new static(static::generateIdentifierString(
            $type->toNative(),
            $id->toNative()
        ));
    }

    /**
     * @param Advantage $advantage
     * @return static
     */
    public static function fromAdvantage(Advantage $advantage)
    {
        return static::fromAdvantageTypeAndId($advantage->getType(), $advantage->getId());
    }

    /**
     * @param $type
     * @param $id
     *
     * @return string
     */
    private static function generateIdentifierString($type, $id)
    {
        return $type . self::SEPARATOR . $id;
    }
}
