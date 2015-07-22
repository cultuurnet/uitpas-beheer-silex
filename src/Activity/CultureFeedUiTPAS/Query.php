<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultureFeed_Uitpas_Event_Query_SearchEventsOptions;
use CultuurNet\Clock\Clock;
use CultuurNet\UiTPASBeheer\Activity\DateType;
use CultuurNet\UiTPASBeheer\Activity\SimpleQuery;
use ValueObjects\Number\Integer;

class Query extends SimpleQuery implements SearchOptionsBuilderInterface
{
    /**
     * @var Clock
     */
    private $clock;

    /**
     * @param Clock $clock
     */
    public function __construct($clock)
    {
        $this->clock = $clock;
    }

    /**
     * @inheritdoc
     */
    public function build()
    {
        $options = new CultureFeed_Uitpas_Event_Query_SearchEventsOptions();

        if ($this->dateType) {
            if ($this->dateType->is(DateType::PAST())) {
                $options->datetype = null;
                $options->endDate = $this->getPastEndDate()->getTimestamp();
                $options->startDate = $this->getPastStartDate()->getTimeStamp();
            } else {
                $options->datetype = str_replace('_', '', $this->dateType->toNative());
                $options->endDate = null;
            }
        }

        if ($this->sort) {
            $options->sort = 'permanent desc,availableto asc';
        }

        if ($this->limit && $this->page) {
            $options->max = $this->limit->toNative();
            $options->start = $this->calculateStart($this->limit, $this->page)->toNative();
        }

        if ($this->query) {
            $options->q = $this->query->toNative();
        }

        if ($this->uitpasNumber) {
            $options->uitpasNumber = $this->uitpasNumber->toNative();
        }

        return $options;
    }

    /**
     * @return \DateTimeInterface
     */
    private function getPastStartDate()
    {
        $now = $this->clock->getDateTime();
        $date = \DateTime::createFromFormat(
            \DateTime::W3C,
            $now->format(\DateTime::W3C)
        );
        $date->modify('-366 day');
        $date->setTime(0, 0, 0);

        return $date;
    }

    /**
     * @return \DateTimeInterface
     */
    private function getPastEndDate()
    {
        $now = $this->clock->getDateTime();

        $date = \DateTime::createFromFormat(
            \DateTime::W3C,
            $now->format(\DateTime::W3C)
        );

        $date->modify('-1 day');
        $date->setTime(23, 59, 59);

        return $date;
    }

    /**
     * @param \ValueObjects\Number\Integer $limit
     * @param \ValueObjects\Number\Integer $page
     *
     * @return \ValueObjects\Number\Integer
     */
    private function calculateStart(Integer $limit, Integer $page)
    {
        return new Integer($limit->toNative() * ($page->toNative() - 1));
    }
}
