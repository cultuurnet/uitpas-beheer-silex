<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Search\QueryBuilderInterface;

interface PassHolderIteratorFactoryInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param int $limitPerPage
     * @return \Iterator
     */
    public function search(QueryBuilderInterface $queryBuilder, $limitPerPage = 20);
}
