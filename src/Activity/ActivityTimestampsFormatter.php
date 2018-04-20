<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use IntlDateFormatter;

class ActivityTimestampsFormatter
{
    private $fmt;

    private $fmtDay;

    public function __construct()
    {
        $this->fmt = new IntlDateFormatter(
            'nl_BE',
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'd MMMM yyyy'
        );

        $this->fmtDay = new IntlDateFormatter(
            'nl_BE',
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'eeee'
        );
    }

    public function format(array $timestamps) {
        $totalTimestamps = count($timestamps);

        if ($totalTimestamps == 1) {
            $timestamp = $timestamps[0];
            return $this->formatSingleTimestamp($timestamp);
        } else {
            return $this->formatMultipleTimestamps($timestamps, $totalTimestamps);
        }
    }

    public function formatSingleTimestamp($timestamp)
    {
        $date = $timestamp->date;
        $intlDate = $this->fmt->format($date);
        $intlDateDay = $this->fmtDay->format($date);

        $output = $intlDateDay . ' ' . $intlDate;

        return $output;
    }

    public function formatMultipleTimestamps($timestamps, $totalTimestamps)
    {
        $output = '';

        /** @var \CultureFeed_Uitpas_Calendar_Timestamp $timestamp */
        foreach ($timestamps as $i => $timestamp) {
            if ($i == 0 || $i == $totalTimestamps-1) {
                $date = $timestamp->date;
                $intlDate =$this->fmt->format($date);

                if ($i == 0) {
                    $output .= 'Van ';
                }
                $output .= $intlDate;
                if ($i == 0) {
                    $firstDate = $intlDate;
                    $output .= PHP_EOL . 'tot ';
                }
            }
            if ($i == $totalTimestamps-1) {
                if ($firstDate == $intlDate) {
                    $intlDateDay = $this->fmtDay->format($date);
                    $output = $intlDateDay . ' ' . $intlDate;
                }
            }
        }

        return $output;
    }
}
