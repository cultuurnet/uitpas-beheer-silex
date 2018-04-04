<?php

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
            if ($this->dateType->is(DateType::PAST)) {
                $options->datetype = null;
                $options->endDate = $this->getPastEndDate()->getTimestamp();
                $options->startDate = $this->getPastStartDate()->getTimeStamp();
            } elseif ($this->dateType->is(DateType::CHOOSE_DATE)) {
                $options->datetype = null;
                $options->startDate = $this->getDateRangeStartDate($this->startDate->toNative());
                $options->endDate = $this->getDateRangeEndDate($this->endDate->toNative());
            } else {
                $options->datetype = str_replace('_', '', $this->dateType->toNative());
                $options->endDate = null;
            }
        }

        if ($this->sort) {
            $options->sort = $this->sort;
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

    /**
     * @param int $timestamp
     *
     * @return string
     */
    private function getDateRangeStartDate($timestamp)
    {
        $startDateTime = new \DateTime();

        if ($timestamp) {
            $startDateTime->setTimestamp($timestamp);
        } else {
            $startDateTime->modify('-10 years');
        }

        $startDateTime->setTime(0, 0, 0);

        return $startDateTime->format(\DateTime::W3C);
    }

    /**
     * @param int $timestamp
     *
     * @return string
     */
    private function getDateRangeEndDate($timestamp)
    {
        $endDateTime = new \DateTime();

        if ($timestamp) {
            $endDateTime->setTimestamp($timestamp);
        } else {
            $endDateTime->modify('+10 years');
        }

        $endDateTime->setTime(23, 59, 59);

        return $endDateTime->format(\DateTime::W3C);
    }
}
