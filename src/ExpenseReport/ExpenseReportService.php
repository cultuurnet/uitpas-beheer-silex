<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultureFeed_Uitpas_Calendar_Period;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use CultuurNet\UiTPASBeheer\Properties\DateRange;
use DateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ValueObjects\DateTime\Date;
use ValueObjects\Web\Url;

class ExpenseReportService extends CounterAwareUitpasService implements ExpenseReportServiceInterface
{
    /**
     * Route name for downloading an expense report.
     */
    const DOWNLOAD_ROUTE_NAME = 'uitpas.expense-report.download';

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        \CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct(
            $uitpasService,
            $counterConsumerKey
        );
        $this->urlGenerator = $urlGenerator;
    }

    public function getPeriods()
    {
        date_default_timezone_set('Europe/Brussels');

        $cfPeriods = $this->getUitpasService()->getFinancialOverviewReportPeriods(
            $this->getCounterConsumerKey()
        );

        return array_map(
            function (CultureFeed_Uitpas_Calendar_Period $cfPeriod) {
                return new DateRange(
                    Date::fromNativeDateTime(
                        (new DateTime())->setTimestamp($cfPeriod->datefrom)
                    ),
                    Date::fromNativeDateTime(
                        (new DateTime())->setTimestamp($cfPeriod->dateto)
                    )
                );
            },
            $cfPeriods
        );
    }

    /**
     * @param DateRange $dateRange
     * @return ExpenseReportInfo
     */
    public function generate(DateRange $dateRange)
    {
        $from = $dateRange->getFrom()->toNativeDateTime();
        $to = $dateRange->getTo()->toNativeDateTime();

        $id = $this->getUitpasService()->generateFinancialOverviewReport(
            $from,
            $to,
            $this->getCounterConsumerKey()
        );

        return new ExpenseReportInfo(
            new ExpenseReportId((string) $id),
            $dateRange
        );
    }

    /**
     * @param ExpenseReportId $id
     * @return ExpenseReportStatus
     */
    public function getStatus(ExpenseReportId $id)
    {
        $cfStatus = $this->getUitpasService()->financialOverviewReportStatus(
            $id->toNative(),
            $this->getCounterConsumerKey()
        );

        if ($cfStatus->completed()) {
            $url = $this->urlGenerator->generate(
                self::DOWNLOAD_ROUTE_NAME,
                ['expenseReportId' => $id->toNative()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $url = Url::fromNative($url);

            return ExpenseReportStatus::complete($url);
        }

        return ExpenseReportStatus::incomplete();
    }
}
