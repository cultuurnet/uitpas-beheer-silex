<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\UiTPASBeheer\Properties\DateRangeJsonDeserializer;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class ExpenseReportControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $app['expense_report_controller'] = $app->share(
            function (Application $app) {
                return new ExpenseReportController(
                    $app['expense_report_service'],
                    $app['expense_report_api'],
                    new DateRangeJsonDeserializer()
                );
            }
        );

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->post('/counters/active/expense-reports', 'expense_report_controller:generate');

        // Needs to be registered before the status url, otherwise it's never matched.
        $controllers->get(
            '/counters/active/expense-reports/{expenseReportId}.zip',
            'expense_report_controller:download'
        )->bind(ExpenseReportService::DOWNLOAD_ROUTE_NAME);

        $controllers->get(
            '/counters/active/expense-reports/{expenseReportId}',
            'expense_report_controller:getStatus'
        );

        return $controllers;
    }
}
