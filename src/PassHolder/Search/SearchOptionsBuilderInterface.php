<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

interface SearchOptionsBuilderInterface
{
    /**
     * @return \CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions
     */
    public function build();
}
