<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class MissingPropertyException extends ReadableCodeResponseException
{
    /**
     * @var string
     */
    protected $property;

    public function __construct($property, $code = 'MISSING_PROPERTY')
    {
        $this->property = $property;
        $message = sprintf('Missing property "%s".', $property);
        parent::__construct($message, $code);
    }

    /**
     * @return string
     */
    public function getMissingProperty()
    {
        return $this->property;
    }

    /**
     * @param $property
     * @param MissingPropertyException $e
     * @return MissingPropertyException
     */
    public static function fromMissingChildPropertyException($property, MissingPropertyException $e)
    {
        $property = $property . '->' . $e->getMissingProperty();
        return new MissingPropertyException($property, $e->getReadableCode());
    }
}
