<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Search\QueryBuilderInterface;

interface PassHolderIteratorFactoryInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @return \Iterator
     */
    public function search(QueryBuilderInterface $queryBuilder);
}
