<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use ValueObjects\StringLiteral\StringLiteral;

class Activity implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var StringLiteral
     */
    protected $title;

    /**
     * @param StringLiteral $id
     * @param StringLiteral $title
     */
    public function __construct(
        StringLiteral $id,
        StringLiteral $title
    ) {
        $this->id = $id;
        $this->title = $title;
    }

    /**
     * @param \CultureFeed_Uitpas_Event_CultureEvent $event
     * @return static
     */
    public static function fromCultureFeedUitpasEvent(\CultureFeed_Uitpas_Event_CultureEvent $event)
    {
        $id = new StringLiteral((string) $event->cdbid);
        $title = new StringLiteral((string) $event->title);

        return new static(
          $id,
          $title
        );
    }

    /**
     * @return StringLiteral
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return StringLiteral
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id->toNative(),
            'title' => $this->title->toNative(),
        ];
    }
}
