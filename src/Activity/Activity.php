<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultureFeed_Uitpas_Event_CultureEvent;
use CultureFeed_Cdb_Item_Event;
use CultuurNet\CalendarSummary\CalendarPlainTextFormatter;
use CultuurNet\CalendarSummary\FormatterException;
use ValueObjects\StringLiteral\StringLiteral;

class Activity implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $date;

    /**
     * @var StringLiteral
     */
    protected $description;

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
     * @param StringLiteral $description
     * @param StringLiteral $date
     */
    public function __construct(
        StringLiteral $id,
        StringLiteral $title,
        StringLiteral $description,
        StringLiteral $date
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
    }

    /**
     * @param CultureFeed_Uitpas_Event_CultureEvent $uitpasEvent
     * @param CultureFeed_Cdb_Item_Event $cdbEvent
     * @return static
     */
    public static function fromCultureFeedUitpasAndCdbEvent(
        CultureFeed_Uitpas_Event_CultureEvent $uitpasEvent,
        CultureFeed_Cdb_Item_Event $cdbEvent = null
    ) {
        $id = new StringLiteral((string) $uitpasEvent->cdbid);
        $title = new StringLiteral((string) $uitpasEvent->title);
        $description = new StringLiteral('');
        $date = new StringLiteral('');

        if ($cdbEvent) {
            // Description.
            $details = $cdbEvent->getDetails()->getDetailByLanguage('nl');
            $description = new StringLiteral((string) $details->getShortDescription());

            // Calendar.
            $calendar = $cdbEvent->getCalendar();
            $formatter = new CalendarPlainTextFormatter();

            try {
                $date = new StringLiteral(
                    (string)$formatter->format($calendar, 'xs')
                );
            }
            catch (FormatterException $e) {
                // Format not supported for the calendar type, for example for a
                // CultureFeed_Cdb_Data_Calendar_TimestampList. Leave date empty
                // in this case.
                $date = new StringLiteral('');
            }
        }

        return new static(
            $id,
            $title,
            $description,
            $date
        );
    }

    /**
     * @return StringLiteral
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return StringLiteral
     */
    public function getDescription()
    {
        return $this->description;
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
            'description' => $this->description->toNative(),
            'date' => $this->date->toNative(),
        ];
    }
}
