<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use CultureFeed_ResultSet;
use CultureFeed_Uitpas;
use CultureFeed_Uitpas_Counter_Employee;
use CultureFeed_Uitpas_Counter_EmployeeCardSystem;
use CultureFeed_Uitpas_Counter_Query_SearchCounterOptions;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Counter\CounterServiceInterface;
use ValueObjects\StringLiteral\StringLiteral;

class SchoolService extends CounterAwareUitpasService implements SchoolServiceInterface
{
    /**
     * @var CounterServiceInterface
     */
    protected $counters;

    /**
     * SchoolService constructor.
     * @param CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     * @param CounterServiceInterface $counters
     */
    public function __construct(
        CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey,
        CounterServiceInterface $counters
    ) {
        parent::__construct($uitpasService, $counterConsumerKey);

        $this->counters = $counters;
    }

    /**
     * @inheritdoc
     */
    public function get(StringLiteral $uuid)
    {
        $school = null;

        $conditions = new CultureFeed_Uitpas_Counter_Query_SearchCounterOptions();
        $conditions->key = $uuid->toNative();

        /** @var CultureFeed_ResultSet $countersResultSet */
        $countersResultSet = $this->getUitpasService()->searchCounters(
            $conditions
        );

        $cfCounter = reset($countersResultSet->objects);

        if ($cfCounter) {
            $school = School::fromCultureFeedCounter($cfCounter);
        }

        return $school;
    }

    /**
     * @param CultureFeed_Uitpas_Counter_Employee $counter
     * @return string[]
     */
    private function getCardSystemIds(CultureFeed_Uitpas_Counter_Employee $counter)
    {
        return array_map(
            function (CultureFeed_Uitpas_Counter_EmployeeCardSystem $cardSystem) {
                return $cardSystem->id;
            },
            $counter->cardSystems
        );
    }

    /**
     * @inheritdoc
     */
    public function getSchools()
    {
        $conditions = new CultureFeed_Uitpas_Counter_Query_SearchCounterOptions();
        $conditions->school = true;

        $activeCounter = $this->counters->getActiveCounter();
        $conditions->cardSystemId = $this->getCardSystemIds($activeCounter);
        $conditions->start = 0;
        $conditions->max = 1000;

        /** @var CultureFeed_ResultSet $countersResultSet */
        $countersResultSet = $this->getUitpasService()->searchCounters(
            $conditions
        );

        return SchoolCollection::fromCultureFeedCounters(
            $countersResultSet->objects
        );
    }
}
