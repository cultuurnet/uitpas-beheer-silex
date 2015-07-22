<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

interface SearchOptionsBuilderInterface
{
    /**
     * @return \CultureFeed_Uitpas_Event_Query_SearchEventsOptions
     */
    public function build();
}
