<?php

namespace CultuurNet\UiTPASBeheer\Advantage\CashIn;

use CultuurNet\UiTPASBeheer\Advantage\AdvantageType;
use ValueObjects\StringLiteral\StringLiteral;

class CashInContentType extends StringLiteral
{
    const FORMAT = '^application\\/vnd\\+cultuurnet\\.uitpas\\.advantage\\.(.+?)\\.cash-in\\+json(;.+)?$';

    /**
     * @var AdvantageType
     */
    protected $type;

    /**
     * @param string $value
     *
     * @throws CashInContentTypeInvalidException
     *   When the provided value is not a valid content type for cashing an advantage.
     */
    public function __construct($value)
    {
        $pattern = '/' . self::FORMAT . '/';
        $matches = [];

        preg_match($pattern, $value, $matches);

        if (count($matches) < 2) {
            throw new CashInContentTypeInvalidException($value);
        }

        try {
            $this->type = AdvantageType::get($matches[1]);
        } catch (\InvalidArgumentException $exception) {
            throw new CashInContentTypeInvalidException($value);
        }

        parent::__construct($value);
    }

    /**
     * @return AdvantageType
     */
    public function getAdvantageType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return string
     */
    private function generateContentTypeString($type)
    {
        return 'application/vnd+cultuurnet.uitpas.advantage.' . $type . '.cash-in+json';
    }

    /**
     * @param AdvantageType $advantageType
     * @return static
     */
    public static function fromAdvantageType(AdvantageType $advantageType)
    {
        $contentType = self::generateContentTypeString($advantageType->toNative());
        return new static($contentType);
    }
}
